<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\Order;
use common\models\OrderItem;
use common\models\Part;
use common\models\Customer;
use common\models\Payment;

/**
 * OrderController - จัดการคำสั่งซื้อ
 */
class OrderController extends Controller
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
                    'confirm' => ['POST'],
                    'prepare' => ['POST'],
                    'ship' => ['POST'],
                    'deliver' => ['POST'],
                    'cancel' => ['POST'],
                    'verify-payment' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * รายการคำสั่งซื้อทั้งหมด
     */
    public function actionIndex()
    {
        $searchModel = new \backend\models\OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Order stats
        $stats = [
            'pending' => Order::find()->where(['status' => 'pending'])->count(),
            'confirmed' => Order::find()->where(['status' => 'confirmed'])->count(),
            'preparing' => Order::find()->where(['status' => 'preparing'])->count(),
            'shipped' => Order::find()->where(['status' => 'shipped'])->count(),
            'unpaid' => Order::find()->where(['payment_status' => ['unpaid', 'partial']])->andWhere(['not', ['status' => 'cancelled']])->count(),
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'stats' => $stats,
        ]);
    }

    /**
     * แสดงรายละเอียดคำสั่งซื้อ
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Payment history
        $payments = Payment::find()
            ->where(['order_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'payments' => $payments,
        ]);
    }

    /**
     * สร้างคำสั่งซื้อใหม่ (สำหรับ Walk-in / Phone Order)
     */
    public function actionCreate()
    {
        $model = new Order();
        $model->status = 'pending';
        $model->payment_status = 'unpaid';
        $model->shipping_fee = 0;
        $model->discount_amount = 0;

        if ($model->load(Yii::$app->request->post())) {
            $model->order_number = Order::generateOrderNumber();
            
            if ($model->save()) {
                // Save order items
                $items = Yii::$app->request->post('OrderItem', []);
                $this->saveOrderItems($model->id, $items);
                
                // Calculate totals
                $model->calculateTotals();
                $model->save(false);
                
                Yii::$app->session->setFlash('success', 'สร้างคำสั่งซื้อ ' . $model->order_number . ' เรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'customers' => Customer::find()->where(['is_active' => 1])->all(),
            'parts' => Part::find()->where(['is_active' => 1])->andWhere(['>', 'stock_quantity', 0])->all(),
        ]);
    }

    /**
     * แก้ไขคำสั่งซื้อ (เฉพาะ pending/confirmed เท่านั้น)
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if (!in_array($model->status, ['pending', 'confirmed'])) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถแก้ไขคำสั่งซื้อที่อยู่ในสถานะนี้ได้');
            return $this->redirect(['view', 'id' => $id]);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                // Update order items
                $items = Yii::$app->request->post('OrderItem', []);
                $this->saveOrderItems($model->id, $items, true);
                
                // Recalculate totals
                $model->calculateTotals();
                $model->save(false);
                
                Yii::$app->session->setFlash('success', 'บันทึกการแก้ไขเรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'customers' => Customer::find()->where(['is_active' => 1])->all(),
            'parts' => Part::find()->where(['is_active' => 1])->all(),
        ]);
    }

    /**
     * ยืนยันคำสั่งซื้อ
     */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);
        
        if ($model->confirm()) {
            Yii::$app->session->setFlash('success', 'ยืนยันคำสั่งซื้อเรียบร้อยแล้ว');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถยืนยันคำสั่งซื้อได้: ' . implode(', ', $model->getFirstErrors()));
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * เริ่มเตรียมสินค้า
     */
    public function actionPrepare($id)
    {
        $model = $this->findModel($id);
        
        if ($model->markPreparing()) {
            Yii::$app->session->setFlash('success', 'เปลี่ยนสถานะเป็น "กำลังเตรียมสินค้า" เรียบร้อยแล้ว');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถเปลี่ยนสถานะได้');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * จัดส่งสินค้า
     */
    public function actionShip($id)
    {
        $model = $this->findModel($id);
        
        $trackingNumber = Yii::$app->request->post('tracking_number');
        $shippingMethod = Yii::$app->request->post('shipping_method');
        
        if (empty($trackingNumber)) {
            Yii::$app->session->setFlash('error', 'กรุณาระบุเลขพัสดุ');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        if ($model->ship($trackingNumber, $shippingMethod)) {
            Yii::$app->session->setFlash('success', 'บันทึกการจัดส่งเรียบร้อยแล้ว เลขพัสดุ: ' . $trackingNumber);
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถบันทึกการจัดส่งได้');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * ยืนยันส่งถึงแล้ว
     */
    public function actionDeliver($id)
    {
        $model = $this->findModel($id);
        
        if ($model->markDelivered()) {
            Yii::$app->session->setFlash('success', 'ยืนยันการส่งสินค้าถึงลูกค้าเรียบร้อยแล้ว');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถยืนยันการส่งได้');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * ยกเลิกคำสั่งซื้อ
     */
    public function actionCancel($id)
    {
        $model = $this->findModel($id);
        $reason = Yii::$app->request->post('reason', '');
        
        if ($model->cancel($reason)) {
            Yii::$app->session->setFlash('success', 'ยกเลิกคำสั่งซื้อเรียบร้อยแล้ว');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถยกเลิกคำสั่งซื้อได้');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * บันทึกการชำระเงิน
     */
    public function actionAddPayment($id)
    {
        $order = $this->findModel($id);
        
        $payment = new Payment();
        $payment->order_id = $id;
        $payment->status = 'pending';
        
        if ($payment->load(Yii::$app->request->post())) {
            // Handle slip upload
            $slip = \yii\web\UploadedFile::getInstance($payment, 'slip_image');
            if ($slip) {
                $uploadPath = Yii::getAlias('@webroot/uploads/payments/');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $filename = 'payment_' . time() . '.' . $slip->extension;
                if ($slip->saveAs($uploadPath . $filename)) {
                    $payment->slip_image = $filename;
                }
            }
            
            if ($payment->save()) {
                Yii::$app->session->setFlash('success', 'บันทึกการชำระเงินเรียบร้อยแล้ว รอตรวจสอบ');
                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('add-payment', [
            'order' => $order,
            'payment' => $payment,
        ]);
    }

    /**
     * ตรวจสอบการชำระเงิน
     */
    public function actionVerifyPayment($payment_id)
    {
        $payment = Payment::findOne($payment_id);
        if (!$payment) {
            throw new NotFoundHttpException('ไม่พบข้อมูลการชำระเงิน');
        }
        
        $action = Yii::$app->request->post('action');
        $reason = Yii::$app->request->post('reason', '');
        
        if ($action === 'approve') {
            if ($payment->verify(Yii::$app->user->id)) {
                Yii::$app->session->setFlash('success', 'อนุมัติการชำระเงินเรียบร้อยแล้ว');
            }
        } elseif ($action === 'reject') {
            if ($payment->reject(Yii::$app->user->id, $reason)) {
                Yii::$app->session->setFlash('warning', 'ปฏิเสธการชำระเงินเรียบร้อยแล้ว');
            }
        }
        
        return $this->redirect(['view', 'id' => $payment->order_id]);
    }

    /**
     * พิมพ์ใบสั่งซื้อ
     */
    public function actionPrint($id)
    {
        $model = $this->findModel($id);
        
        $this->layout = 'print';
        return $this->render('print', [
            'model' => $model,
        ]);
    }

    /**
     * Export to Excel
     */
    public function actionExport()
    {
        $query = Order::find()
            ->with(['customer', 'items'])
            ->orderBy(['created_at' => SORT_DESC]);
        
        // Apply filters from request
        $dateFrom = Yii::$app->request->get('date_from');
        $dateTo = Yii::$app->request->get('date_to');
        $status = Yii::$app->request->get('status');
        
        if ($dateFrom) {
            $query->andWhere(['>=', 'created_at', strtotime($dateFrom)]);
        }
        if ($dateTo) {
            $query->andWhere(['<=', 'created_at', strtotime($dateTo . ' 23:59:59')]);
        }
        if ($status) {
            $query->andWhere(['status' => $status]);
        }
        
        $orders = $query->all();
        
        // Generate CSV
        $filename = 'orders_export_' . date('Ymd_His') . '.csv';
        $headers = ['เลขที่', 'วันที่', 'ลูกค้า', 'สถานะ', 'ยอดรวม', 'การชำระ', 'ขนส่ง', 'เลขพัสดุ'];
        
        $content = "\xEF\xBB\xBF"; // UTF-8 BOM
        $content .= implode(',', $headers) . "\n";
        
        foreach ($orders as $order) {
            $statusLabels = [
                'pending' => 'รอยืนยัน',
                'confirmed' => 'ยืนยันแล้ว',
                'preparing' => 'กำลังเตรียม',
                'shipped' => 'จัดส่งแล้ว',
                'delivered' => 'ส่งถึงแล้ว',
                'cancelled' => 'ยกเลิก',
            ];
            $paymentLabels = [
                'unpaid' => 'ยังไม่ชำระ',
                'partial' => 'ชำระบางส่วน',
                'paid' => 'ชำระแล้ว',
                'refunded' => 'คืนเงิน',
            ];
            
            $row = [
                $order->order_number,
                date('Y-m-d H:i', $order->created_at),
                '"' . ($order->customer ? $order->customer->getDisplayName() : '-') . '"',
                $statusLabels[$order->status] ?? $order->status,
                number_format($order->grand_total, 2),
                $paymentLabels[$order->payment_status] ?? $order->payment_status,
                $order->shipping_method ?: '-',
                $order->tracking_number ?: '-',
            ];
            $content .= implode(',', $row) . "\n";
        }
        
        return Yii::$app->response->sendContentAsFile($content, $filename, [
            'mimeType' => 'text/csv',
            'inline' => false,
        ]);
    }

    /**
     * Save order items
     */
    protected function saveOrderItems($orderId, $items, $deleteExisting = false)
    {
        if ($deleteExisting) {
            OrderItem::deleteAll(['order_id' => $orderId]);
        }
        
        foreach ($items as $itemData) {
            if (empty($itemData['part_id']) || empty($itemData['quantity'])) {
                continue;
            }
            
            $part = Part::findOne($itemData['part_id']);
            if (!$part) continue;
            
            $item = new OrderItem();
            $item->order_id = $orderId;
            $item->part_id = $itemData['part_id'];
            $item->quantity = $itemData['quantity'];
            $item->unit_price = $itemData['unit_price'] ?? $part->getCurrentPrice();
            $item->discount = $itemData['discount'] ?? 0;
            $item->line_total = ($item->unit_price * $item->quantity) - $item->discount;
            
            // Set warranty
            $item->warranty_days = $part->warranty_days;
            
            $item->save();
        }
    }

    /**
     * Find model by ID
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('ไม่พบคำสั่งซื้อที่ต้องการ');
    }
}
