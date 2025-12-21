<?php

use yii\db\Migration;

/**
 * Class m241218_000002_seed_data
 * สร้างข้อมูลเริ่มต้น
 */
class m241218_000002_seed_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create admin user
        $this->insert('{{%user}}', [
            'username' => 'admin',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('admin123'),
            'email_address' => 'admin@dunesautoparts.com',
            'full_name' => 'ผู้ดูแลระบบ',
            'role' => 'admin',
            'user_status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        // Create staff user
        $this->insert('{{%user}}', [
            'username' => 'staff',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('staff123'),
            'email_address' => 'staff@dunesautoparts.com',
            'full_name' => 'พนักงาน ทดสอบ',
            'role' => 'staff',
            'user_status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        // Create vehicle brands
        $brands = [
            ['name_th' => 'โตโยต้า', 'name_en' => 'Toyota', 'logo' => null, 'country' => 'Japan', 'sort_order' => 1, 'is_active' => 1],
            ['name_th' => 'ฮอนด้า', 'name_en' => 'Honda', 'logo' => null, 'country' => 'Japan', 'sort_order' => 2, 'is_active' => 1],
            ['name_th' => 'อีซูซุ', 'name_en' => 'Isuzu', 'logo' => null, 'country' => 'Japan', 'sort_order' => 3, 'is_active' => 1],
            ['name_th' => 'มิตซูบิชิ', 'name_en' => 'Mitsubishi', 'logo' => null, 'country' => 'Japan', 'sort_order' => 4, 'is_active' => 1],
            ['name_th' => 'นิสสัน', 'name_en' => 'Nissan', 'logo' => null, 'country' => 'Japan', 'sort_order' => 5, 'is_active' => 1],
            ['name_th' => 'มาสด้า', 'name_en' => 'Mazda', 'logo' => null, 'country' => 'Japan', 'sort_order' => 6, 'is_active' => 1],
            ['name_th' => 'ซูซูกิ', 'name_en' => 'Suzuki', 'logo' => null, 'country' => 'Japan', 'sort_order' => 7, 'is_active' => 1],
            ['name_th' => 'ฟอร์ด', 'name_en' => 'Ford', 'logo' => null, 'country' => 'USA', 'sort_order' => 8, 'is_active' => 1],
            ['name_th' => 'เชฟโรเลต', 'name_en' => 'Chevrolet', 'logo' => null, 'country' => 'USA', 'sort_order' => 9, 'is_active' => 1],
            ['name_th' => 'เมอร์เซเดส-เบนซ์', 'name_en' => 'Mercedes-Benz', 'logo' => null, 'country' => 'Germany', 'sort_order' => 10, 'is_active' => 1],
            ['name_th' => 'บีเอ็มดับเบิลยู', 'name_en' => 'BMW', 'logo' => null, 'country' => 'Germany', 'sort_order' => 11, 'is_active' => 1],
            ['name_th' => 'อาวดี้', 'name_en' => 'Audi', 'logo' => null, 'country' => 'Germany', 'sort_order' => 12, 'is_active' => 1],
            ['name_th' => 'โฟล์คสวาเกน', 'name_en' => 'Volkswagen', 'logo' => null, 'country' => 'Germany', 'sort_order' => 13, 'is_active' => 1],
            ['name_th' => 'โวลโว่', 'name_en' => 'Volvo', 'logo' => null, 'country' => 'Sweden', 'sort_order' => 14, 'is_active' => 1],
            ['name_th' => 'ฮุนได', 'name_en' => 'Hyundai', 'logo' => null, 'country' => 'South Korea', 'sort_order' => 15, 'is_active' => 1],
            ['name_th' => 'เกีย', 'name_en' => 'Kia', 'logo' => null, 'country' => 'South Korea', 'sort_order' => 16, 'is_active' => 1],
            ['name_th' => 'เอ็มจี', 'name_en' => 'MG', 'logo' => null, 'country' => 'UK', 'sort_order' => 17, 'is_active' => 1],
        ];

        foreach ($brands as $brand) {
            $this->insert('{{%vehicle_brand}}', $brand);
        }

        // Create part categories
        $categories = [
            ['name_th' => 'เครื่องยนต์', 'name_en' => 'Engine', 'parent_id' => null, 'sort_order' => 1, 'is_active' => 1],
            ['name_th' => 'ระบบเบรค', 'name_en' => 'Brake System', 'parent_id' => null, 'sort_order' => 2, 'is_active' => 1],
            ['name_th' => 'ระบบกันสะเทือน', 'name_en' => 'Suspension', 'parent_id' => null, 'sort_order' => 3, 'is_active' => 1],
            ['name_th' => 'ระบบไฟฟ้า', 'name_en' => 'Electrical', 'parent_id' => null, 'sort_order' => 4, 'is_active' => 1],
            ['name_th' => 'ระบบปรับอากาศ', 'name_en' => 'Air Conditioning', 'parent_id' => null, 'sort_order' => 5, 'is_active' => 1],
            ['name_th' => 'ตัวถัง/ภายนอก', 'name_en' => 'Body/Exterior', 'parent_id' => null, 'sort_order' => 6, 'is_active' => 1],
            ['name_th' => 'ภายในห้องโดยสาร', 'name_en' => 'Interior', 'parent_id' => null, 'sort_order' => 7, 'is_active' => 1],
            ['name_th' => 'ระบบส่งกำลัง', 'name_en' => 'Transmission', 'parent_id' => null, 'sort_order' => 8, 'is_active' => 1],
            ['name_th' => 'ระบบระบายความร้อน', 'name_en' => 'Cooling System', 'parent_id' => null, 'sort_order' => 9, 'is_active' => 1],
            ['name_th' => 'ระบบเชื้อเพลิง', 'name_en' => 'Fuel System', 'parent_id' => null, 'sort_order' => 10, 'is_active' => 1],
            ['name_th' => 'ระบบไอเสีย', 'name_en' => 'Exhaust System', 'parent_id' => null, 'sort_order' => 11, 'is_active' => 1],
            ['name_th' => 'น้ำมันและสารหล่อลื่น', 'name_en' => 'Oil & Lubricants', 'parent_id' => null, 'sort_order' => 12, 'is_active' => 1],
            ['name_th' => 'ยางและล้อ', 'name_en' => 'Tires & Wheels', 'parent_id' => null, 'sort_order' => 13, 'is_active' => 1],
            ['name_th' => 'อุปกรณ์เสริม', 'name_en' => 'Accessories', 'parent_id' => null, 'sort_order' => 14, 'is_active' => 1],
        ];

        foreach ($categories as $category) {
            $this->insert('{{%part_category}}', $category);
        }

        // Create suppliers
        $suppliers = [
            // ซัพพลายเออร์ในประเทศ
            [
                'code' => 'SUP-001',
                'name' => 'บริษัท ไทยออโต้พาร์ท จำกัด',
                'contact_name' => 'คุณสมชาย ใจดี',
                'phone' => '02-123-4567',
                'email' => 'sales@thaiautopartz.co.th',
                'address' => '123/45 ถนนพระราม 2 แขวงแสมดำ เขตบางขุนเทียน กรุงเทพฯ 10150',
                'country' => 'Thailand',
                'supplier_type' => 'local',
                'payment_terms' => 'เครดิต 30 วัน',
                'notes' => 'ตัวแทนจำหน่ายอะไหล่ Toyota, Honda',
                'is_active' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'code' => 'SUP-002',
                'name' => 'ห้างหุ้นส่วนจำกัด เจริญยนต์',
                'contact_name' => 'คุณวิชัย เจริญกิจ',
                'phone' => '081-234-5678',
                'email' => 'charoenyont@gmail.com',
                'address' => '88 ซอยลาดพร้าว 101 แขวงคลองจั่น เขตบางกะปิ กรุงเทพฯ 10240',
                'country' => 'Thailand',
                'supplier_type' => 'local',
                'payment_terms' => 'เงินสด / เครดิต 15 วัน',
                'notes' => 'อะไหล่เครื่องยนต์ทุกยี่ห้อ ราคาส่ง',
                'is_active' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'code' => 'SUP-003',
                'name' => 'อู่ช่างเล็ก ออโต้พาร์ท',
                'contact_name' => 'คุณเล็ก',
                'phone' => '089-876-5432',
                'email' => null,
                'address' => '55/3 หมู่ 5 ต.บางพลีใหญ่ อ.บางพลี จ.สมุทรปราการ 10540',
                'country' => 'Thailand',
                'supplier_type' => 'local',
                'payment_terms' => 'เงินสด',
                'notes' => 'อะไหล่มือสองคัดเกรด ราคาถูก',
                'is_active' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'code' => 'SUP-004',
                'name' => 'บริษัท ศูนย์รวมอะไหล่ จำกัด',
                'contact_name' => 'คุณนภา ศรีสุข',
                'phone' => '02-987-6543',
                'email' => 'info@partscenter.co.th',
                'address' => '199/88 ถนนบางนา-ตราด กม.3 แขวงบางนา เขตบางนา กรุงเทพฯ 10260',
                'country' => 'Thailand',
                'supplier_type' => 'local',
                'payment_terms' => 'เครดิต 45 วัน',
                'notes' => 'ศูนย์รวมอะไหล่แท้ทุกยี่ห้อ Denso, Aisin, NTN',
                'is_active' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            // ซัพพลายเออร์ญี่ปุ่น
            [
                'code' => 'SUP-005',
                'name' => 'Japan Auto Parts Co., Ltd.',
                'contact_name' => 'Mr. Tanaka',
                'phone' => '+81-3-1234-5678',
                'email' => 'export@japautoparts.jp',
                'address' => '1-2-3 Shinagawa, Tokyo 140-0001, Japan',
                'country' => 'Japan',
                'supplier_type' => 'japan',
                'payment_terms' => 'T/T 30 days',
                'notes' => 'อะไหล่มือสองนำเข้าจากญี่ปุ่น เกรด A+ ทุกชิ้น',
                'is_active' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'code' => 'SUP-006',
                'name' => 'Osaka Trading Corporation',
                'contact_name' => 'Mr. Yamamoto',
                'phone' => '+81-6-9876-5432',
                'email' => 'parts@osakatrading.co.jp',
                'address' => '5-6-7 Namba, Osaka 556-0011, Japan',
                'country' => 'Japan',
                'supplier_type' => 'japan',
                'payment_terms' => 'L/C 60 days',
                'notes' => 'เครื่องยนต์และเกียร์มือสอง สภาพดี ไมล์น้อย',
                'is_active' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'code' => 'SUP-007',
                'name' => 'Nagoya Parts Export Inc.',
                'contact_name' => 'Ms. Suzuki',
                'phone' => '+81-52-111-2222',
                'email' => 'suzuki@nagoyaparts.jp',
                'address' => '8-9-10 Sakae, Nagoya 460-0008, Japan',
                'country' => 'Japan',
                'supplier_type' => 'japan',
                'payment_terms' => 'T/T 50% deposit',
                'notes' => 'อะไหล่ตัวถังและไฟฟ้า นำเข้าตรงจากโรงงาน',
                'is_active' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            // ซัพพลายเออร์ยุโรป
            [
                'code' => 'SUP-008',
                'name' => 'Euro Parts GmbH',
                'contact_name' => 'Mr. Schmidt',
                'phone' => '+49-30-1234567',
                'email' => 'order@europarts.de',
                'address' => 'Berliner Str. 123, 10115 Berlin, Germany',
                'country' => 'Germany',
                'supplier_type' => 'europe',
                'payment_terms' => 'Net 30',
                'notes' => 'อะไหล่ Mercedes-Benz, BMW, Audi แท้และเทียบ',
                'is_active' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'code' => 'SUP-009',
                'name' => 'Autoparts Europe BV',
                'contact_name' => 'Mr. Van Der Berg',
                'phone' => '+31-20-5551234',
                'email' => 'sales@autopartseurope.nl',
                'address' => 'Amstelweg 45, 1012 AB Amsterdam, Netherlands',
                'country' => 'Netherlands',
                'supplier_type' => 'europe',
                'payment_terms' => 'Net 45',
                'notes' => 'อะไหล่ Volvo, VW แท้ ส่งตรงจากยุโรป',
                'is_active' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [
                'code' => 'SUP-010',
                'name' => 'UK Motor Spares Ltd.',
                'contact_name' => 'Mr. Williams',
                'phone' => '+44-20-7946-0958',
                'email' => 'exports@ukmotorspares.co.uk',
                'address' => '15 Industrial Estate, Birmingham B1 2AB, UK',
                'country' => 'UK',
                'supplier_type' => 'europe',
                'payment_terms' => 'Net 30',
                'notes' => 'อะไหล่รถอังกฤษ MG, Land Rover, Jaguar',
                'is_active' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
        ];

        foreach ($suppliers as $supplier) {
            $this->insert('{{%supplier}}', $supplier);
        }

        // Create system settings
        $settings = [
            ['setting_key' => 'company_name', 'setting_value' => 'ดูน ออโต้ พาร์ท', 'setting_type' => 'text', 'setting_group' => 'general', 'description' => 'ชื่อบริษัท'],
            ['setting_key' => 'company_phone', 'setting_value' => '02-XXX-XXXX', 'setting_type' => 'text', 'setting_group' => 'general', 'description' => 'เบอร์โทรศัพท์'],
            ['setting_key' => 'company_email', 'setting_value' => 'info@dunesautoparts.com', 'setting_type' => 'text', 'setting_group' => 'general', 'description' => 'อีเมล'],
            ['setting_key' => 'company_address', 'setting_value' => 'กรุงเทพมหานคร', 'setting_type' => 'textarea', 'setting_group' => 'general', 'description' => 'ที่อยู่'],
            ['setting_key' => 'company_tax_id', 'setting_value' => '', 'setting_type' => 'text', 'setting_group' => 'general', 'description' => 'เลขประจำตัวผู้เสียภาษี'],
            ['setting_key' => 'business_hours_open', 'setting_value' => '08:30', 'setting_type' => 'text', 'setting_group' => 'business', 'description' => 'เวลาเปิดทำการ'],
            ['setting_key' => 'business_hours_close', 'setting_value' => '17:30', 'setting_type' => 'text', 'setting_group' => 'business', 'description' => 'เวลาปิดทำการ'],
            ['setting_key' => 'shipping_cutoff', 'setting_value' => '14:00', 'setting_type' => 'text', 'setting_group' => 'business', 'description' => 'เวลาตัดรอบจัดส่ง'],
            ['setting_key' => 'low_stock_threshold', 'setting_value' => '5', 'setting_type' => 'number', 'setting_group' => 'inventory', 'description' => 'จำนวนสินค้าน้อย (แจ้งเตือน)'],
            ['setting_key' => 'default_warranty_days', 'setting_value' => '7', 'setting_type' => 'number', 'setting_group' => 'inventory', 'description' => 'วันรับประกันสินค้ามือสอง (วัน)'],
            ['setting_key' => 'order_prefix', 'setting_value' => 'SO', 'setting_type' => 'text', 'setting_group' => 'order', 'description' => 'รหัสนำหน้าเลขที่คำสั่งซื้อ'],
            ['setting_key' => 'customer_prefix', 'setting_value' => 'CUST', 'setting_type' => 'text', 'setting_group' => 'customer', 'description' => 'รหัสนำหน้าเลขที่ลูกค้า'],
            ['setting_key' => 'chatbot_enabled', 'setting_value' => '1', 'setting_type' => 'boolean', 'setting_group' => 'chatbot', 'description' => 'เปิดใช้งาน AI Chatbot'],
            ['setting_key' => 'chatbot_welcome_message', 'setting_value' => 'สวัสดีครับ! ยินดีให้บริการครับ มีอะไรให้ช่วยครับ?', 'setting_type' => 'textarea', 'setting_group' => 'chatbot', 'description' => 'ข้อความต้อนรับ Chatbot'],
        ];

        foreach ($settings as $setting) {
            $this->insert('{{%setting}}', $setting);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}');
        $this->delete('{{%supplier}}');
        $this->delete('{{%part_category}}');
        $this->delete('{{%vehicle_brand}}');
        $this->delete('{{%user}}', ['username' => ['admin', 'staff']]);
        
        return true;
    }
}
