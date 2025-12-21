<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\Inquiry;
use common\models\InquiryMessage;
use common\models\Customer;
use common\models\Order;
use common\models\Part;

/**
 * InquiryController - จัดการสอบถามลูกค้า (Chat/Message Management)
 */
class InquiryController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'send-message' => ['POST'],
                    'assign' => ['POST'],
                    'close' => ['POST'],
                    'quote' => ['POST'],
                    'convert-to-order' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * รายการสอบถามทั้งหมด
     */
    public function actionIndex()
    {
        $searchModel = new \backend\models\InquirySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Stats
        $stats = [
            'open' => Inquiry::find()->where(['status' => 'open'])->count(),
            'in_progress' => Inquiry::find()->where(['status' => 'in_progress'])->count(),
            'quoted' => Inquiry::find()->where(['status' => 'quoted'])->count(),
            'unassigned' => Inquiry::find()->where(['assigned_to' => null])->andWhere(['in', 'status', ['open', 'in_progress']])->count(),
            'today' => Inquiry::find()->where(['>=', 'created_at', strtotime('today')])->count(),
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'stats' => $stats,
        ]);
    }

    /**
     * หน้าแชท - แสดงการสนทนาและตอบกลับลูกค้า
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Mark as in progress if open
        if ($model->status === 'open') {
            $model->status = 'in_progress';
            $model->save(false);
        }
        
        // Get messages
        $messages = InquiryMessage::find()
            ->where(['inquiry_id' => $id])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
        
        // Get customer's previous orders
        $previousOrders = [];
        if ($model->customer_id) {
            $previousOrders = Order::find()
                ->where(['customer_id' => $model->customer_id])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(5)
                ->all();
        }
        
        // Suggested parts based on vehicle info
        $suggestedParts = [];
        if ($model->vehicle_info) {
            $vehicleInfo = json_decode($model->vehicle_info, true);
            if (!empty($vehicleInfo['brand']) || !empty($vehicleInfo['model'])) {
                $suggestedParts = Part::find()
                    ->joinWith('partVehicles.vehicleModel.brand')
                    ->where(['part.is_active' => 1])
                    ->andWhere(['>', 'part.stock_quantity', 0])
                    ->limit(10)
                    ->all();
            }
        }

        return $this->render('view', [
            'model' => $model,
            'messages' => $messages,
            'previousOrders' => $previousOrders,
            'suggestedParts' => $suggestedParts,
        ]);
    }

    /**
     * สร้าง Inquiry ใหม่ (Manual)
     */
    public function actionCreate()
    {
        $model = new Inquiry();
        $model->status = 'open';
        $model->channel = 'phone';
        $model->priority = 'normal';

        if ($model->load(Yii::$app->request->post())) {
            $model->inquiry_number = Inquiry::generateInquiryNumber();
            
            // Handle vehicle info
            $vehicleInfo = Yii::$app->request->post('vehicle_info', []);
            if (!empty($vehicleInfo)) {
                $model->vehicle_info = json_encode($vehicleInfo, JSON_UNESCAPED_UNICODE);
            }
            
            // Handle requested parts
            $requestedParts = Yii::$app->request->post('requested_parts', []);
            if (!empty($requestedParts)) {
                $model->requested_parts = json_encode($requestedParts, JSON_UNESCAPED_UNICODE);
            }
            
            if ($model->save()) {
                // Add initial message if provided
                $initialMessage = Yii::$app->request->post('initial_message');
                if (!empty($initialMessage)) {
                    $model->addMessage($initialMessage, 'customer');
                }
                
                Yii::$app->session->setFlash('success', 'สร้างรายการสอบถามเรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'customers' => Customer::find()->where(['is_active' => 1])->all(),
        ]);
    }

    /**
     * ส่งข้อความตอบกลับลูกค้า
     */
    public function actionSendMessage($id)
    {
        $model = $this->findModel($id);
        
        $message = Yii::$app->request->post('message');
        $senderType = Yii::$app->request->post('sender_type', 'staff');
        
        if (empty($message)) {
            Yii::$app->session->setFlash('error', 'กรุณาระบุข้อความ');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Handle image attachment
        $attachment = \yii\web\UploadedFile::getInstanceByName('attachment');
        $attachmentPath = null;
        if ($attachment) {
            $uploadPath = Yii::getAlias('@webroot/uploads/chat/');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $filename = 'chat_' . $id . '_' . time() . '.' . $attachment->extension;
            if ($attachment->saveAs($uploadPath . $filename)) {
                $attachmentPath = $filename;
            }
        }
        
        $model->addMessage($message, $senderType, Yii::$app->user->id, $attachmentPath);
        
        // If it was bot message, also send to external channel (Line, FB)
        // This would be implemented with webhook integration
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * มอบหมายให้พนักงาน
     */
    public function actionAssign($id)
    {
        $model = $this->findModel($id);
        $userId = Yii::$app->request->post('user_id');
        
        if ($model->assignTo($userId)) {
            Yii::$app->session->setFlash('success', 'มอบหมายงานเรียบร้อยแล้ว');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถมอบหมายงานได้');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * เสนอราคา
     */
    public function actionQuote($id)
    {
        $model = $this->findModel($id);
        
        $quotedAmount = Yii::$app->request->post('quoted_amount');
        $quoteDetails = Yii::$app->request->post('quote_details');
        
        if ($model->markAsQuoted($quotedAmount)) {
            // Send quote message to customer
            $quoteMessage = "📋 ใบเสนอราคา\n";
            $quoteMessage .= "────────────────\n";
            $quoteMessage .= $quoteDetails . "\n";
            $quoteMessage .= "────────────────\n";
            $quoteMessage .= "💰 รวมทั้งสิ้น: " . number_format($quotedAmount, 2) . " บาท\n\n";
            $quoteMessage .= "หากสนใจสั่งซื้อ กรุณาแจ้งยืนยันครับ 🙏";
            
            $model->addMessage($quoteMessage, 'staff', Yii::$app->user->id);
            
            Yii::$app->session->setFlash('success', 'ส่งใบเสนอราคาเรียบร้อยแล้ว');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถส่งใบเสนอราคาได้');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * แปลงเป็นคำสั่งซื้อ
     */
    public function actionConvertToOrder($id)
    {
        $model = $this->findModel($id);
        
        // Create or find customer
        $customer = $model->findOrCreateCustomer();
        
        if (!$customer) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถสร้างข้อมูลลูกค้าได้ กรุณาระบุข้อมูลติดต่อ');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Create new order
        $order = new Order();
        $order->customer_id = $customer->id;
        $order->order_number = Order::generateOrderNumber();
        $order->order_status = 'pending';
        $order->payment_status = 'unpaid';
        $order->shipping_address = $customer->address;
        $order->notes = "จาก Inquiry #{$model->inquiry_number}";
        $order->subtotal = $model->quoted_amount ?? 0;
        $order->shipping_cost = 0;
        $order->discount_amount = 0;
        $order->grand_total = $model->quoted_amount ?? 0;
        
        if ($order->save()) {
            // Link inquiry to order
            $model->convertToOrder($order->id);
            
            Yii::$app->session->setFlash('success', 'สร้างคำสั่งซื้อ ' . $order->order_number . ' เรียบร้อยแล้ว');
            return $this->redirect(['/order/update', 'id' => $order->id]);
        }
        
        Yii::$app->session->setFlash('error', 'ไม่สามารถสร้างคำสั่งซื้อได้');
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * ปิด Inquiry
     */
    public function actionClose($id)
    {
        $model = $this->findModel($id);
        $reason = Yii::$app->request->post('reason', '');
        
        if ($model->close($reason)) {
            Yii::$app->session->setFlash('success', 'ปิดรายการสอบถามเรียบร้อยแล้ว');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถปิดรายการได้');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * ลบ Inquiry
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Delete messages first
        InquiryMessage::deleteAll(['inquiry_id' => $id]);
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'ลบรายการสอบถามเรียบร้อยแล้ว');
        return $this->redirect(['index']);
    }

    /**
     * Quick reply templates
     */
    public function actionQuickReply()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $templates = [
            'greeting' => "สวัสดีครับ Dune's Auto Parts ยินดีให้บริการครับ 🙏",
            'ask_vehicle' => "รบกวนขอทราบข้อมูลรถเพิ่มเติมครับ:\n- ยี่ห้อ/รุ่น\n- ปีรถ\n- เครื่องยนต์",
            'check_stock' => "รับทราบครับ เดี๋ยวตรวจสอบสต็อกให้สักครู่ครับ 🔍",
            'has_stock' => "ตรวจสอบเบื้องต้นมีของครับ เดี๋ยวเช็คราคาให้ครับ",
            'no_stock' => "ขออภัยครับ สินค้าตัวนี้หมดสต็อกชั่วคราวครับ จะติดต่อกลับเมื่อของเข้าครับ",
            'send_photo' => "ได้ครับ สักครู่นะครับ เดี๋ยวส่งรูปสินค้าจริงให้ชมครับ 📷",
            'shipping' => "📦 การจัดส่ง:\n- ส่ง Kerry/Flash/EMS ทุกวัน\n- ตัดรอบ 14:00 น.\n- มีบริการเก็บเงินปลายทาง (COD)",
            'warranty_new' => "✅ อะไหล่ใหม่: รับประกัน 6 เดือน - 1 ปี",
            'warranty_used' => "✅ มือสองนำเข้า: รับประกันการใช้งาน 7-14 วัน",
            'thanks' => "ขอบคุณที่ใช้บริการครับ 🙏 หากมีข้อสงสัยเพิ่มเติมสอบถามได้ตลอดครับ",
            'closing' => "ร้านเปิดทุกวัน 08:30-17:30 น. ครับ\nLine: @dunesautoparts\nโทร: 0xx-xxx-xxxx",
        ];
        
        return $templates;
    }

    /**
     * AJAX: Search parts for suggestion
     */
    public function actionSearchParts($q)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $parts = Part::find()
            ->where(['is_active' => 1])
            ->andWhere(['>', 'stock_quantity', 0])
            ->andWhere(['or',
                ['like', 'sku', $q],
                ['like', 'name_th', $q],
                ['like', 'name_en', $q],
                ['like', 'oem_number', $q],
            ])
            ->limit(20)
            ->all();
        
        $result = [];
        foreach ($parts as $part) {
            $result[] = [
                'id' => $part->id,
                'sku' => $part->sku,
                'name' => $part->name_th ?: $part->name_en,
                'price' => $part->getCurrentPrice(),
                'stock' => $part->stock_quantity,
                'type' => $part->part_type,
            ];
        }
        
        return $result;
    }

    /**
     * Find model by ID
     */
    protected function findModel($id)
    {
        if (($model = Inquiry::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('ไม่พบรายการสอบถามที่ต้องการ');
    }
}
