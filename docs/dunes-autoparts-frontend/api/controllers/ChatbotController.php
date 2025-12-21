<?php
namespace api\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\Part;
use common\models\Inquiry;
use common\models\InquiryMessage;
use common\models\Customer;
use common\models\VehicleBrand;
use common\models\VehicleModel;
use common\models\PartCategory;
use common\models\Setting;

/**
 * ChatbotController - API สำหรับ AI Chatbot ประจำร้าน "Dune's Auto Parts"
 * 
 * ใช้สำหรับเชื่อมต่อกับ:
 * - Line Messaging API
 * - Facebook Messenger
 * - Website Chat Widget
 * - External AI Systems (OpenAI, Claude, etc.)
 */
class ChatbotController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        
        // API Key authentication for external systems
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'optional' => ['webhook', 'line-webhook', 'facebook-webhook'],
        ];
        
        return $behaviors;
    }

    /**
     * ข้อมูลร้าน - สำหรับให้ AI ใช้ตอบลูกค้า
     */
    public function actionShopInfo()
    {
        $settings = Setting::find()->indexBy('setting_key')->all();
        
        return [
            'success' => true,
            'data' => [
                'shop_name' => 'Dune\'s Auto Parts (ดูน ออโต้ พาร์ท)',
                'description' => 'ร้านจำหน่ายอะไหล่รถยนต์ ทั้งของใหม่และมือสองนำเข้า',
                'business_hours' => [
                    'open' => $settings['shop_open_time']->setting_value ?? '08:30',
                    'close' => $settings['shop_close_time']->setting_value ?? '17:30',
                    'days' => 'เปิดทุกวัน',
                ],
                'shipping' => [
                    'cutoff_time' => $settings['shipping_cutoff_time']->setting_value ?? '14:00',
                    'methods' => ['Kerry', 'Flash', 'EMS', 'J&T'],
                    'cod_available' => true,
                ],
                'services' => [
                    'installation' => false,
                    'note' => 'เราเป็นร้านขายอะไหล่อย่างเดียว ไม่มีบริการติดตั้ง',
                ],
                'product_types' => [
                    [
                        'type' => 'new',
                        'name_th' => 'อะไหล่ใหม่',
                        'description' => 'อะไหล่แท้เบิกศูนย์ (Genuine) และอะไหล่เทียบเกรด OEM คุณภาพสูง',
                        'warranty' => '6 เดือน - 1 ปี',
                    ],
                    [
                        'type' => 'used_imported',
                        'name_th' => 'มือสองนำเข้า',
                        'description' => 'อะไหล่เก่าจากญี่ปุ่น/ยุโรป (เซียงกง) คัดสภาพเกรด A+',
                        'warranty' => '7-14 วัน',
                        'note' => 'ไม่ใช่อะไหล่ซากในไทย เป็นของถอดจากรถวิ่งน้อย มี QC ก่อนส่ง',
                    ],
                ],
                'vehicle_brands' => VehicleBrand::find()
                    ->select(['id', 'name', 'name_th'])
                    ->where(['is_active' => 1])
                    ->orderBy(['name' => SORT_ASC])
                    ->asArray()
                    ->all(),
                'part_categories' => PartCategory::find()
                    ->select(['id', 'name', 'name_th'])
                    ->where(['is_active' => 1])
                    ->orderBy(['sort_order' => SORT_ASC])
                    ->asArray()
                    ->all(),
            ],
        ];
    }

    /**
     * ค้นหาอะไหล่
     */
    public function actionSearchParts()
    {
        $request = Yii::$app->request;
        $keyword = $request->get('keyword', $request->post('keyword'));
        $brandId = $request->get('brand_id', $request->post('brand_id'));
        $modelId = $request->get('model_id', $request->post('model_id'));
        $categoryId = $request->get('category_id', $request->post('category_id'));
        $partType = $request->get('part_type', $request->post('part_type'));
        $year = $request->get('year', $request->post('year'));
        $limit = min((int)$request->get('limit', 10), 50);
        
        $query = Part::find()
            ->with(['category', 'supplier'])
            ->where(['is_active' => 1])
            ->andWhere(['>', 'stock_quantity', 0]);
        
        // Keyword search
        if (!empty($keyword)) {
            $query->andWhere(['or',
                ['like', 'name', $keyword],
                ['like', 'name_th', $keyword],
                ['like', 'sku', $keyword],
                ['like', 'oem_number', $keyword],
                ['like', 'description', $keyword],
            ]);
        }
        
        // Filter by category
        if (!empty($categoryId)) {
            $query->andWhere(['category_id' => $categoryId]);
        }
        
        // Filter by part type
        if (!empty($partType)) {
            $query->andWhere(['part_type' => $partType]);
        }
        
        // Filter by vehicle compatibility
        if (!empty($modelId)) {
            $query->joinWith('partVehicles')
                ->andWhere(['part_vehicle.vehicle_model_id' => $modelId]);
        } elseif (!empty($brandId)) {
            $query->joinWith('partVehicles.vehicleModel')
                ->andWhere(['vehicle_model.brand_id' => $brandId]);
        }
        
        $parts = $query->limit($limit)->all();
        
        $result = [];
        foreach ($parts as $part) {
            $result[] = [
                'id' => $part->id,
                'sku' => $part->sku,
                'name_th' => $part->name_th,
                'name_en' => $part->name_en,
                'oem_number' => $part->oem_number,
                'part_type' => $part->part_type,
                'part_type_th' => $part->part_type == 'new' ? 'ของใหม่' : 'มือสองนำเข้า',
                'condition_grade' => $part->condition_grade,
                'category' => $part->category ? [
                    'id' => $part->category->id,
                    'name_th' => $part->category->name_th,
                    'name_en' => $part->category->name_en,
                ] : null,
                'price' => [
                    'selling' => (float)$part->selling_price,
                    'discount' => $part->discount_price ? (float)$part->discount_price : null,
                    'current' => (float)$part->getCurrentPrice(),
                    'discount_percent' => $part->getDiscountPercent(),
                ],
                'stock' => [
                    'quantity' => $part->stock_quantity,
                    'status' => $part->isInStock() ? ($part->isLowStock() ? 'low' : 'available') : 'out_of_stock',
                ],
                'warranty' => [
                    'days' => $part->warranty_days,
                    'text' => $part->getWarrantyText(),
                ],
                'image' => $part->main_image ? Yii::getAlias('@web/uploads/parts/' . $part->main_image) : null,
                'description' => $part->description,
                'specifications' => $part->specifications ? json_decode($part->specifications, true) : null,
            ];
        }
        
        return [
            'success' => true,
            'count' => count($result),
            'data' => $result,
        ];
    }

    /**
     * ดึงรุ่นรถตามยี่ห้อ
     */
    public function actionVehicleModels($brand_id)
    {
        $models = VehicleModel::find()
            ->where(['brand_id' => $brand_id, 'is_active' => 1])
            ->orderBy(['name_th' => SORT_ASC])
            ->all();
        
        $result = [];
        foreach ($models as $model) {
            $result[] = [
                'id' => $model->id,
                'name' => $model->name_th,
                'generation' => $model->generation,
                'year_range' => $model->year_start . '-' . ($model->year_end ?: 'ปัจจุบัน'),
                'body_type' => $model->body_type,
            ];
        }
        
        return [
            'success' => true,
            'data' => $result,
        ];
    }

    /**
     * สร้าง/ต่อเนื่อง Inquiry จาก Chat
     */
    public function actionCreateInquiry()
    {
        $request = Yii::$app->request;
        
        $inquiry = new Inquiry();
        $inquiry->inquiry_number = Inquiry::generateInquiryNumber();
        $inquiry->channel = $request->post('channel', 'website');
        $inquiry->status = 'open';
        $inquiry->priority = 'normal';
        
        // Customer info
        $inquiry->customer_name = $request->post('customer_name');
        $inquiry->customer_phone = $request->post('customer_phone');
        $inquiry->customer_email = $request->post('customer_email');
        $inquiry->customer_line_id = $request->post('customer_line_id');
        
        // External IDs for webhook integration
        $externalId = $request->post('external_id'); // Line User ID, FB PSID, etc.
        if ($externalId) {
            // Try to find existing customer by external ID
            $customer = Customer::find()
                ->where(['or',
                    ['line_id' => $externalId],
                    ['facebook_id' => $externalId],
                ])
                ->one();
            
            if ($customer) {
                $inquiry->customer_id = $customer->id;
                $inquiry->customer_name = $customer->getDisplayName();
                $inquiry->customer_phone = $customer->phone;
            }
        }
        
        // Vehicle info
        $vehicleInfo = $request->post('vehicle_info');
        if ($vehicleInfo) {
            $inquiry->vehicle_info = is_array($vehicleInfo) 
                ? json_encode($vehicleInfo, JSON_UNESCAPED_UNICODE) 
                : $vehicleInfo;
        }
        
        // Requested parts
        $requestedParts = $request->post('requested_parts');
        if ($requestedParts) {
            $inquiry->requested_parts = is_array($requestedParts) 
                ? json_encode($requestedParts, JSON_UNESCAPED_UNICODE) 
                : $requestedParts;
        }
        
        if ($inquiry->save()) {
            // Add initial message if provided
            $message = $request->post('message');
            if ($message) {
                $inquiry->addMessage($message, 'customer');
            }
            
            return [
                'success' => true,
                'data' => [
                    'inquiry_id' => $inquiry->id,
                    'inquiry_number' => $inquiry->inquiry_number,
                ],
            ];
        }
        
        return [
            'success' => false,
            'errors' => $inquiry->errors,
        ];
    }

    /**
     * เพิ่มข้อความใน Inquiry
     */
    public function actionAddMessage()
    {
        $request = Yii::$app->request;
        $inquiryId = $request->post('inquiry_id');
        $message = $request->post('message');
        $senderType = $request->post('sender_type', 'customer');
        
        $inquiry = Inquiry::findOne($inquiryId);
        if (!$inquiry) {
            return [
                'success' => false,
                'error' => 'Inquiry not found',
            ];
        }
        
        $msgModel = $inquiry->addMessage($message, $senderType);
        
        return [
            'success' => true,
            'data' => [
                'message_id' => $msgModel->id,
                'created_at' => date('Y-m-d H:i:s', $msgModel->created_at),
            ],
        ];
    }

    /**
     * ดึงประวัติการสนทนา
     */
    public function actionGetMessages($inquiry_id)
    {
        $inquiry = Inquiry::findOne($inquiry_id);
        if (!$inquiry) {
            return ['success' => false, 'error' => 'Inquiry not found'];
        }
        
        $messages = InquiryMessage::find()
            ->where(['inquiry_id' => $inquiry_id])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
        
        $result = [];
        foreach ($messages as $msg) {
            $result[] = [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_type' => $msg->sender_type,
                'attachment' => $msg->attachment,
                'created_at' => date('Y-m-d H:i:s', $msg->created_at),
            ];
        }
        
        return [
            'success' => true,
            'inquiry' => [
                'id' => $inquiry->id,
                'number' => $inquiry->inquiry_number,
                'status' => $inquiry->status,
                'vehicle_info' => $inquiry->vehicle_info ? json_decode($inquiry->vehicle_info, true) : null,
                'quoted_amount' => $inquiry->quoted_amount,
            ],
            'messages' => $result,
        ];
    }

    /**
     * AI Response Generator - สร้างคำตอบอัตโนมัติสำหรับ Chatbot
     * 
     * รับข้อความจากลูกค้า แล้วสร้างคำตอบตาม persona ของร้าน
     */
    public function actionGenerateResponse()
    {
        $request = Yii::$app->request;
        $userMessage = $request->post('message');
        $inquiryId = $request->post('inquiry_id');
        $context = $request->post('context', []);
        
        // Get inquiry context if exists
        $inquiryContext = [];
        if ($inquiryId) {
            $inquiry = Inquiry::findOne($inquiryId);
            if ($inquiry) {
                $inquiryContext = [
                    'vehicle_info' => $inquiry->vehicle_info ? json_decode($inquiry->vehicle_info, true) : null,
                    'requested_parts' => $inquiry->requested_parts ? json_decode($inquiry->requested_parts, true) : null,
                    'status' => $inquiry->status,
                    'previous_messages' => InquiryMessage::find()
                        ->where(['inquiry_id' => $inquiryId])
                        ->orderBy(['created_at' => SORT_DESC])
                        ->limit(5)
                        ->asArray()
                        ->all(),
                ];
            }
        }
        
        // Analyze user intent
        $intent = $this->analyzeIntent($userMessage);
        
        // Generate appropriate response
        $response = $this->buildResponse($intent, $userMessage, $inquiryContext, $context);
        
        // Save bot message if inquiry exists
        if ($inquiryId && $inquiry) {
            $inquiry->addMessage($response['message'], 'bot');
        }
        
        return [
            'success' => true,
            'data' => $response,
        ];
    }

    /**
     * Line Webhook - รับข้อความจาก Line Messaging API
     */
    public function actionLineWebhook()
    {
        $request = Yii::$app->request;
        $body = $request->rawBody;
        
        // Verify Line signature
        $signature = $request->headers->get('X-Line-Signature');
        $channelSecret = Yii::$app->params['lineChannelSecret'] ?? '';
        
        if (!$this->verifyLineSignature($body, $signature, $channelSecret)) {
            Yii::$app->response->statusCode = 401;
            return ['error' => 'Invalid signature'];
        }
        
        $events = json_decode($body, true)['events'] ?? [];
        
        foreach ($events as $event) {
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $this->handleLineMessage($event);
            }
        }
        
        return ['success' => true];
    }

    /**
     * Facebook Webhook - รับข้อความจาก Facebook Messenger
     */
    public function actionFacebookWebhook()
    {
        $request = Yii::$app->request;
        
        // Verification challenge
        if ($request->isGet) {
            $mode = $request->get('hub_mode');
            $token = $request->get('hub_verify_token');
            $challenge = $request->get('hub_challenge');
            
            $verifyToken = Yii::$app->params['fbVerifyToken'] ?? '';
            
            if ($mode === 'subscribe' && $token === $verifyToken) {
                Yii::$app->response->format = Response::FORMAT_RAW;
                return $challenge;
            }
            
            Yii::$app->response->statusCode = 403;
            return 'Forbidden';
        }
        
        // Handle incoming messages
        $body = json_decode($request->rawBody, true);
        $entries = $body['entry'] ?? [];
        
        foreach ($entries as $entry) {
            $messaging = $entry['messaging'] ?? [];
            foreach ($messaging as $event) {
                if (isset($event['message']['text'])) {
                    $this->handleFacebookMessage($event);
                }
            }
        }
        
        return ['success' => true];
    }

    /**
     * Analyze user intent from message
     */
    protected function analyzeIntent($message)
    {
        $message = mb_strtolower($message);
        
        // Greeting patterns
        if (preg_match('/(สวัสดี|หวัดดี|ดีครับ|ดีค่ะ|hello|hi)/u', $message)) {
            return 'greeting';
        }
        
        // Price inquiry
        if (preg_match('/(ราคา|เท่าไหร่|เท่าไร|กี่บาท|price)/u', $message)) {
            return 'price_inquiry';
        }
        
        // Stock check
        if (preg_match('/(มีของ|มีไหม|มีมั้ย|สต็อก|พร้อมส่ง|in stock)/u', $message)) {
            return 'stock_check';
        }
        
        // Photo request
        if (preg_match('/(ขอรูป|ส่งรูป|ดูรูป|รูปสินค้า|photo|picture)/u', $message)) {
            return 'photo_request';
        }
        
        // Shipping inquiry
        if (preg_match('/(ส่ง|จัดส่ง|shipping|ค่าส่ง|กี่วัน|delivery)/u', $message)) {
            return 'shipping_inquiry';
        }
        
        // Warranty inquiry
        if (preg_match('/(ประกัน|รับประกัน|warranty|เคลม)/u', $message)) {
            return 'warranty_inquiry';
        }
        
        // Part search with vehicle info
        if (preg_match('/(คอมแอร์|หม้อน้ำ|ผ้าเบรก|ช่วงล่าง|ไดชาร์จ|ไดสตาร์ท|เครื่อง|กรอง)/u', $message)) {
            return 'part_search';
        }
        
        // Thanks / Closing
        if (preg_match('/(ขอบคุณ|thanks|thank you|โอเค|ok)/u', $message)) {
            return 'thanks';
        }
        
        return 'general';
    }

    /**
     * Build response based on intent
     */
    protected function buildResponse($intent, $userMessage, $inquiryContext, $additionalContext)
    {
        $responses = [
            'greeting' => [
                'message' => "สวัสดีครับ Dune's Auto Parts ยินดีให้บริการครับ 🙏\nสนใจอะไหล่รุ่นไหนครับ?",
                'quick_replies' => ['ค้นหาอะไหล่', 'เช็คราคา', 'สอบถามการจัดส่ง'],
            ],
            'price_inquiry' => [
                'message' => "รับทราบครับ รบกวนขอทราบข้อมูลรถเพิ่มเติมนะครับ:\n- ยี่ห้อ/รุ่นรถ\n- ปีรถ\n- เครื่องยนต์ (ถ้าทราบ)\n\nเพื่อเช็คราคาได้ถูกต้องครับ",
                'action' => 'collect_vehicle_info',
                'quick_replies' => ['Honda', 'Toyota', 'Benz', 'BMW', 'อื่นๆ'],
            ],
            'stock_check' => [
                'message' => "ตรวจสอบเบื้องต้นมีของครับ เดี๋ยวเช็คสต็อกจริงให้สักครู่ครับ 🔍\n\nรบกวนแจ้ง ยี่ห้อ/รุ่น/ปีรถ ด้วยนะครับ",
                'action' => 'check_stock',
            ],
            'photo_request' => [
                'message' => "ได้ครับ สักครู่นะครับ เดี๋ยวส่งรูปสินค้าจริงให้ชมครับ 📷",
                'action' => 'request_photo',
                'needs_human' => true,
            ],
            'shipping_inquiry' => [
                'message' => "📦 ข้อมูลการจัดส่งครับ:\n\n• ส่ง Kerry/Flash/EMS/J&T ทุกวัน\n• ตัดรอบส่ง 14:00 น.\n• มีบริการเก็บเงินปลายทาง (COD)\n• ร้านเปิดทุกวัน 08:30-17:30 น.\n\nสนใจอะไหล่ตัวไหนครับ?",
            ],
            'warranty_inquiry' => [
                'message' => "✅ นโยบายการรับประกันครับ:\n\n📦 อะไหล่ใหม่: รับประกัน 6 เดือน - 1 ปี (ตามผู้ผลิต)\n📦 มือสองนำเข้า: ประกันการใช้งาน 7-14 วัน\n\n*ไม่รับคืนกรณีสั่งผิดรุ่น*\n\nมีอะไรให้ช่วยเพิ่มเติมไหมครับ?",
            ],
            'part_search' => [
                'message' => "รับทราบครับ 🔧\n\nรบกวนขอข้อมูลรถเพิ่มเติมนะครับ:\n- ยี่ห้อ/รุ่นรถ\n- ปีรถ\n- เครื่องยนต์ (ขนาด/รหัส)\n\nเพื่อเช็คของได้ตรงรุ่นครับ",
                'action' => 'collect_vehicle_info',
            ],
            'thanks' => [
                'message' => "ขอบคุณที่ใช้บริการครับ 🙏\nหากมีข้อสงสัยเพิ่มเติม สอบถามได้ตลอดครับ\n\nLine: @dunesautoparts",
            ],
            'general' => [
                'message' => "รับทราบครับ เดี๋ยวตรวจสอบให้สักครู่ครับ 🔍",
                'needs_human' => true,
            ],
        ];
        
        $response = $responses[$intent] ?? $responses['general'];
        $response['intent'] = $intent;
        $response['original_message'] = $userMessage;
        
        // Check if we have vehicle info in context
        if (isset($inquiryContext['vehicle_info']) && !empty($inquiryContext['vehicle_info'])) {
            $vehicleInfo = $inquiryContext['vehicle_info'];
            // If we have complete vehicle info, we can search for parts
            if (!empty($vehicleInfo['brand']) && !empty($vehicleInfo['model'])) {
                $response['has_vehicle_info'] = true;
            }
        }
        
        return $response;
    }

    /**
     * Handle Line message
     */
    protected function handleLineMessage($event)
    {
        $userId = $event['source']['userId'];
        $message = $event['message']['text'];
        $replyToken = $event['replyToken'];
        
        // Find or create inquiry
        $inquiry = Inquiry::find()
            ->where(['customer_line_id' => $userId])
            ->andWhere(['in', 'status', ['open', 'in_progress', 'quoted']])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        if (!$inquiry) {
            $inquiry = new Inquiry();
            $inquiry->inquiry_number = Inquiry::generateInquiryNumber();
            $inquiry->channel = 'line';
            $inquiry->status = 'open';
            $inquiry->customer_line_id = $userId;
            $inquiry->save();
        }
        
        // Add customer message
        $inquiry->addMessage($message, 'customer');
        
        // Generate and send response
        $intent = $this->analyzeIntent($message);
        $response = $this->buildResponse($intent, $message, [], []);
        
        // Add bot response
        $inquiry->addMessage($response['message'], 'bot');
        
        // Send reply via Line API
        // $this->sendLineReply($replyToken, $response['message']);
        
        return true;
    }

    /**
     * Handle Facebook message
     */
    protected function handleFacebookMessage($event)
    {
        $senderId = $event['sender']['id'];
        $message = $event['message']['text'];
        
        // Find or create inquiry
        $inquiry = Inquiry::find()
            ->joinWith('customer')
            ->where(['customer.facebook_id' => $senderId])
            ->andWhere(['in', 'inquiry.status', ['open', 'in_progress', 'quoted']])
            ->orderBy(['inquiry.created_at' => SORT_DESC])
            ->one();
        
        if (!$inquiry) {
            $inquiry = new Inquiry();
            $inquiry->inquiry_number = Inquiry::generateInquiryNumber();
            $inquiry->channel = 'facebook';
            $inquiry->status = 'open';
            $inquiry->save();
        }
        
        // Add customer message
        $inquiry->addMessage($message, 'customer');
        
        // Generate and send response
        $intent = $this->analyzeIntent($message);
        $response = $this->buildResponse($intent, $message, [], []);
        
        // Add bot response
        $inquiry->addMessage($response['message'], 'bot');
        
        // Send reply via Facebook API
        // $this->sendFacebookReply($senderId, $response['message']);
        
        return true;
    }

    /**
     * Verify Line webhook signature
     */
    protected function verifyLineSignature($body, $signature, $channelSecret)
    {
        if (empty($channelSecret)) return true; // Skip if not configured
        
        $hash = base64_encode(hash_hmac('sha256', $body, $channelSecret, true));
        return hash_equals($hash, $signature);
    }
}
