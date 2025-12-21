<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Setting;
use yii\helpers\ArrayHelper;

/**
 * SettingController - จัดการการตั้งค่าระบบ
 */
class SettingController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * หน้าหลักการตั้งค่า
     */
    public function actionIndex()
    {
        $settings = Setting::find()
            ->orderBy(['setting_group' => SORT_ASC, 'setting_key' => SORT_ASC])
            ->all();
        
        // จัดกลุ่มการตั้งค่า
        $groupedSettings = ArrayHelper::index($settings, null, 'setting_group');
        
        // กลุ่มการตั้งค่าที่กำหนดไว้
        $settingGroups = [
            'general' => [
                'label' => 'ทั่วไป',
                'icon' => 'bi-gear',
                'description' => 'การตั้งค่าทั่วไปของร้าน',
            ],
            'shop' => [
                'label' => 'ข้อมูลร้าน',
                'icon' => 'bi-shop',
                'description' => 'ชื่อร้าน ที่อยู่ ข้อมูลติดต่อ',
            ],
            'order' => [
                'label' => 'คำสั่งซื้อ',
                'icon' => 'bi-cart',
                'description' => 'การตั้งค่าเกี่ยวกับคำสั่งซื้อ',
            ],
            'shipping' => [
                'label' => 'การจัดส่ง',
                'icon' => 'bi-truck',
                'description' => 'ค่าจัดส่ง วิธีการจัดส่ง',
            ],
            'payment' => [
                'label' => 'การชำระเงิน',
                'icon' => 'bi-credit-card',
                'description' => 'บัญชีธนาคาร ช่องทางชำระเงิน',
            ],
            'notification' => [
                'label' => 'การแจ้งเตือน',
                'icon' => 'bi-bell',
                'description' => 'การแจ้งเตือน LINE, Email, SMS',
            ],
            'line' => [
                'label' => 'LINE OA',
                'icon' => 'bi-chat-dots',
                'description' => 'การตั้งค่า LINE Official Account',
            ],
            'system' => [
                'label' => 'ระบบ',
                'icon' => 'bi-hdd-stack',
                'description' => 'การตั้งค่าระบบขั้นสูง',
            ],
        ];
        
        return $this->render('index', [
            'groupedSettings' => $groupedSettings,
            'settingGroups' => $settingGroups,
        ]);
    }

    /**
     * บันทึกการตั้งค่า
     */
    public function actionSave()
    {
        $post = Yii::$app->request->post();
        
        if (isset($post['Setting'])) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($post['Setting'] as $key => $value) {
                    $setting = Setting::findOne(['setting_key' => $key]);
                    if ($setting) {
                        $setting->setting_value = is_array($value) ? json_encode($value) : $value;
                        if (!$setting->save()) {
                            throw new \Exception('ไม่สามารถบันทึกการตั้งค่า ' . $key);
                        }
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        
        return $this->redirect(['index']);
    }

    /**
     * เพิ่มการตั้งค่าใหม่
     */
    public function actionCreate()
    {
        $model = new Setting();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'เพิ่มการตั้งค่าเรียบร้อยแล้ว');
            return $this->redirect(['index']);
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * แก้ไขการตั้งค่า
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'แก้ไขการตั้งค่าเรียบร้อยแล้ว');
            return $this->redirect(['index']);
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * ลบการตั้งค่า
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if ($model->is_system) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถลบการตั้งค่าระบบได้');
        } else {
            $model->delete();
            Yii::$app->session->setFlash('success', 'ลบการตั้งค่าเรียบร้อยแล้ว');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * ตั้งค่าเริ่มต้น - สร้างการตั้งค่าพื้นฐาน
     */
    public function actionInitialize()
    {
        $defaultSettings = [
            // ข้อมูลร้าน
            ['setting_group' => 'shop', 'setting_key' => 'shop_name', 'setting_value' => "ดูน ออโต้ พาร์ท", 'setting_type' => 'text', 'setting_label' => 'ชื่อร้าน', 'is_system' => 1],
            ['setting_group' => 'shop', 'setting_key' => 'shop_name_en', 'setting_value' => "Dune's Auto Parts", 'setting_type' => 'text', 'setting_label' => 'ชื่อร้าน (EN)', 'is_system' => 1],
            ['setting_group' => 'shop', 'setting_key' => 'shop_phone', 'setting_value' => '', 'setting_type' => 'text', 'setting_label' => 'เบอร์โทรศัพท์', 'is_system' => 1],
            ['setting_group' => 'shop', 'setting_key' => 'shop_email', 'setting_value' => '', 'setting_type' => 'email', 'setting_label' => 'อีเมล', 'is_system' => 1],
            ['setting_group' => 'shop', 'setting_key' => 'shop_address', 'setting_value' => '', 'setting_type' => 'textarea', 'setting_label' => 'ที่อยู่ร้าน', 'is_system' => 1],
            ['setting_group' => 'shop', 'setting_key' => 'shop_line_id', 'setting_value' => '', 'setting_type' => 'text', 'setting_label' => 'LINE ID', 'is_system' => 1],
            ['setting_group' => 'shop', 'setting_key' => 'shop_facebook', 'setting_value' => '', 'setting_type' => 'url', 'setting_label' => 'Facebook Page', 'is_system' => 1],
            
            // เวลาทำการ
            ['setting_group' => 'shop', 'setting_key' => 'business_hours_open', 'setting_value' => '08:30', 'setting_type' => 'time', 'setting_label' => 'เวลาเปิดร้าน', 'is_system' => 1],
            ['setting_group' => 'shop', 'setting_key' => 'business_hours_close', 'setting_value' => '17:30', 'setting_type' => 'time', 'setting_label' => 'เวลาปิดร้าน', 'is_system' => 1],
            ['setting_group' => 'shop', 'setting_key' => 'shipping_cutoff_time', 'setting_value' => '14:00', 'setting_type' => 'time', 'setting_label' => 'เวลาตัดรอบจัดส่ง', 'is_system' => 1],
            
            // การสั่งซื้อ
            ['setting_group' => 'order', 'setting_key' => 'order_prefix', 'setting_value' => 'ORD', 'setting_type' => 'text', 'setting_label' => 'คำนำหน้าเลขคำสั่งซื้อ', 'is_system' => 1],
            ['setting_group' => 'order', 'setting_key' => 'min_order_amount', 'setting_value' => '0', 'setting_type' => 'number', 'setting_label' => 'ยอดสั่งซื้อขั้นต่ำ', 'is_system' => 1],
            ['setting_group' => 'order', 'setting_key' => 'auto_confirm_order', 'setting_value' => '0', 'setting_type' => 'boolean', 'setting_label' => 'ยืนยันคำสั่งซื้ออัตโนมัติ', 'is_system' => 1],
            
            // การจัดส่ง
            ['setting_group' => 'shipping', 'setting_key' => 'free_shipping_min', 'setting_value' => '0', 'setting_type' => 'number', 'setting_label' => 'ยอดสั่งซื้อขั้นต่ำสำหรับจัดส่งฟรี', 'is_system' => 1],
            ['setting_group' => 'shipping', 'setting_key' => 'default_shipping_cost', 'setting_value' => '50', 'setting_type' => 'number', 'setting_label' => 'ค่าจัดส่งเริ่มต้น', 'is_system' => 1],
            ['setting_group' => 'shipping', 'setting_key' => 'shipping_methods', 'setting_value' => json_encode(['pickup' => 'รับเอง', 'delivery' => 'จัดส่ง', 'kerry' => 'Kerry Express', 'flash' => 'Flash Express', 'thaipost' => 'ไปรษณีย์ไทย']), 'setting_type' => 'json', 'setting_label' => 'วิธีการจัดส่ง', 'is_system' => 1],
            
            // การชำระเงิน
            ['setting_group' => 'payment', 'setting_key' => 'bank_accounts', 'setting_value' => json_encode([]), 'setting_type' => 'json', 'setting_label' => 'บัญชีธนาคาร', 'is_system' => 1],
            ['setting_group' => 'payment', 'setting_key' => 'payment_methods', 'setting_value' => json_encode(['transfer' => 'โอนเงิน', 'cash' => 'เงินสด', 'credit' => 'บัตรเครดิต']), 'setting_type' => 'json', 'setting_label' => 'วิธีการชำระเงิน', 'is_system' => 1],
            
            // LINE OA
            ['setting_group' => 'line', 'setting_key' => 'line_channel_id', 'setting_value' => '', 'setting_type' => 'text', 'setting_label' => 'Channel ID', 'is_system' => 1],
            ['setting_group' => 'line', 'setting_key' => 'line_channel_secret', 'setting_value' => '', 'setting_type' => 'password', 'setting_label' => 'Channel Secret', 'is_system' => 1],
            ['setting_group' => 'line', 'setting_key' => 'line_access_token', 'setting_value' => '', 'setting_type' => 'password', 'setting_label' => 'Channel Access Token', 'is_system' => 1],
            ['setting_group' => 'line', 'setting_key' => 'line_notify_token', 'setting_value' => '', 'setting_type' => 'password', 'setting_label' => 'LINE Notify Token', 'is_system' => 1],
            
            // การแจ้งเตือน
            ['setting_group' => 'notification', 'setting_key' => 'notify_new_order', 'setting_value' => '1', 'setting_type' => 'boolean', 'setting_label' => 'แจ้งเตือนคำสั่งซื้อใหม่', 'is_system' => 1],
            ['setting_group' => 'notification', 'setting_key' => 'notify_payment', 'setting_value' => '1', 'setting_type' => 'boolean', 'setting_label' => 'แจ้งเตือนการชำระเงิน', 'is_system' => 1],
            ['setting_group' => 'notification', 'setting_key' => 'notify_low_stock', 'setting_value' => '1', 'setting_type' => 'boolean', 'setting_label' => 'แจ้งเตือนสินค้าใกล้หมด', 'is_system' => 1],
            ['setting_group' => 'notification', 'setting_key' => 'notify_inquiry', 'setting_value' => '1', 'setting_type' => 'boolean', 'setting_label' => 'แจ้งเตือนสอบถามใหม่', 'is_system' => 1],
            
            // ระบบ
            ['setting_group' => 'system', 'setting_key' => 'default_min_stock', 'setting_value' => '5', 'setting_type' => 'number', 'setting_label' => 'สต็อกขั้นต่ำเริ่มต้น', 'is_system' => 1],
            ['setting_group' => 'system', 'setting_key' => 'default_warranty_days', 'setting_value' => '30', 'setting_type' => 'number', 'setting_label' => 'วันรับประกันเริ่มต้น', 'is_system' => 1],
            ['setting_group' => 'system', 'setting_key' => 'enable_ai_chatbot', 'setting_value' => '0', 'setting_type' => 'boolean', 'setting_label' => 'เปิดใช้งาน AI Chatbot', 'is_system' => 1],
            ['setting_group' => 'system', 'setting_key' => 'timezone', 'setting_value' => 'Asia/Bangkok', 'setting_type' => 'text', 'setting_label' => 'Timezone', 'is_system' => 1],
        ];
        
        $created = 0;
        $skipped = 0;
        
        foreach ($defaultSettings as $settingData) {
            $exists = Setting::findOne(['setting_key' => $settingData['setting_key']]);
            if (!$exists) {
                $setting = new Setting($settingData);
                if ($setting->save()) {
                    $created++;
                }
            } else {
                $skipped++;
            }
        }
        
        Yii::$app->session->setFlash('success', "สร้างการตั้งค่าเริ่มต้นเรียบร้อย (สร้างใหม่: {$created}, มีอยู่แล้ว: {$skipped})");
        return $this->redirect(['index']);
    }

    /**
     * ค้นหา Model
     */
    protected function findModel($id)
    {
        if (($model = Setting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบการตั้งค่าที่ต้องการ');
    }

    /**
     * Helper: ดึงค่าการตั้งค่า
     */
    public static function getValue($key, $default = null)
    {
        $setting = Setting::findOne(['setting_key' => $key]);
        return $setting ? $setting->setting_value : $default;
    }
}
