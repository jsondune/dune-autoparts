<?php

use yii\db\Migration;

/**
 * Dune's Auto Parts - Database Schema
 * ระบบจัดการร้านอะไหล่รถยนต์ครบวงจร
 * 
 * @author Dune's Auto Parts Development Team
 * @since 1.0
 */
class m241218_000001_create_autoparts_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // =====================================================
        // 1. ตาราง User (ผู้ใช้งานระบบ)
        // =====================================================
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
            'email_address' => $this->string(255)->notNull()->unique(),
            'full_name' => $this->string(255),
            'phone_number' => $this->string(20),
            'avatar_file_path' => $this->string(255),
            'role' => $this->string(50)->notNull()->defaultValue('staff'),
            'user_status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-user-status', '{{%users}}', 'status');
        $this->createIndex('idx-user-role', '{{%users}}', 'role');

        // =====================================================
        // 2. ตาราง Vehicle Brand (ยี่ห้อรถยนต์)
        // =====================================================
        $this->createTable('{{%vehicle_brand}}', [
            'id' => $this->primaryKey(),
            'name_th' => $this->string(100)->notNull(),
            'name_en' => $this->string(100),
            'logo' => $this->string(255),
            'country' => $this->string(50),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'sort_order' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-vehicle_brand-is_active', '{{%vehicle_brand}}', 'is_active');

        // =====================================================
        // 3. ตาราง Vehicle Model (รุ่นรถยนต์)
        // =====================================================
        $this->createTable('{{%vehicle_model}}', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer()->notNull(),
            'name_th' => $this->string(100)->notNull(),
            'name_en' => $this->string(100),
            'generation' => $this->string(50), // เช่น FD, FC, FB
            'year_start' => $this->integer(),
            'year_end' => $this->integer(),
            'body_type' => $this->string(50), // Sedan, SUV, Hatchback, Pickup
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-vehicle_model-brand_id',
            '{{%vehicle_model}}',
            'brand_id',
            '{{%vehicle_brand}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // =====================================================
        // 4. ตาราง Engine Type (ประเภทเครื่องยนต์)
        // =====================================================
        $this->createTable('{{%engine_type}}', [
            'id' => $this->primaryKey(),
            'model_id' => $this->integer()->notNull(),
            'engine_code' => $this->string(50)->notNull(), // เช่น R18A, K20A
            'displacement' => $this->decimal(3, 1), // เช่น 1.8, 2.0
            'fuel_type' => $this->string(20), // Gasoline, Diesel, Hybrid
            'power_hp' => $this->integer(),
            'torque_nm' => $this->integer(),
            'year_start' => $this->integer(),
            'year_end' => $this->integer(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-engine_type-model_id',
            '{{%engine_type}}',
            'model_id',
            '{{%vehicle_model}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // =====================================================
        // 5. ตาราง Part Category (หมวดหมู่อะไหล่)
        // =====================================================
        $this->createTable('{{%part_category}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'name_th' => $this->string(100)->notNull(),
            'name_en' => $this->string(100)->notNull(),
            'slug' => $this->string(100)->notNull()->unique(),
            'icon' => $this->string(50), // FontAwesome icon class
            'description' => $this->text(),
            'image' => $this->string(255),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'sort_order' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-part_category-parent_id',
            '{{%part_category}}',
            'parent_id',
            '{{%part_category}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // =====================================================
        // 6. ตาราง Supplier (ซัพพลายเออร์)
        // =====================================================
        $this->createTable('{{%supplier}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(20)->notNull()->unique(),
            'name' => $this->string(255)->notNull(),
            'contact_name' => $this->string(100),
            'phone' => $this->string(20),
            'email' => $this->string(255),
            'address' => $this->text(),
            'country' => $this->string(50),
            'supplier_type' => $this->string(20)->notNull(), // local, japan, europe
            'payment_terms' => $this->string(100),
            'notes' => $this->text(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // =====================================================
        // 7. ตาราง Part (อะไหล่)
        // =====================================================
        $this->createTable('{{%part}}', [
            'id' => $this->primaryKey(),
            'sku' => $this->string(50)->notNull()->unique()->comment('รหัสสินค้า (SKU)'),
            'oem_number' => $this->string(50)->comment('เลขอะไหล่แท้'), 
            'name_th' => $this->string(255)->notNull()->comment('ชื่อสินค้า (TH)'),
            'name_en' => $this->string(255)->notNull()->comment('ชื่อสินค้า (EN)'),
            'category_id' => $this->integer()->notNull()->comment('หมวดหมู่'),
            'brand_manufacturer' => $this->string(100)->comment('ยี่ห้อผู้ผลิตอะไหล่ Denso, Valeo, etc.'), // ยี่ห้อผู้ผลิตอะไหล่ (Denso, Valeo, etc.)
            'part_type' => $this->string(20)->notNull()->comment('ประเภทสินค้า new, used_imported'), // new, used_imported
            'condition_grade' => $this->string(10)->comment('เกรดอะไหล่ A+, A, B'), // A+, A, B (สำหรับมือสอง)
            'origin_country' => $this->string(50)->comment('ประเทศ'), // Japan, Germany, Thailand
            'description' => $this->text()->comment('รายละเอียด'),
            'specifications' => $this->text()->comment('รายละเอียดทางเทคนิค (JSON)'), // JSON: รายละเอียดทางเทคนิค
            'weight_kg' => $this->decimal(8, 2)->comment('น้ำหนัก (กก.)'),
            'dimensions' => $this->string(100)->comment('LxWxH cm'), // LxWxH cm
            'cost_price' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('ราคาทุน'),
            'selling_price' => $this->decimal(12, 2)->notNull()->comment('ราตาขาย'),
            'discount_price' => $this->decimal(12, 2)->comment('ส่วนลด'),
            'warranty_days' => $this->integer()->defaultValue(0)->comment('ระยะเวลารับประกัน (วัน)'),
            'warranty_description' => $this->string(255)->comment('รายละเอียดการรับประกัน'),
            'stock_quantity' => $this->integer()->notNull()->defaultValue(0)->comment('จำนวนในสต็อก'),
            'min_stock_level' => $this->integer()->defaultValue(1)->comment('จำนวนขั้นต่ำ'),
            'location' => $this->string(50)->comment('ตำแหน่งในคลัง'),
            'supplier_id' => $this->integer()->comment('ซัพพลายเออร์'),
            'main_image' => $this->string(255)->comment('รูปอะไหล่ (หลัก)'),
            'images' => $this->text()->comment('รูปอะไหล่ (JSON array)'), // JSON array of image paths
            'tags' => $this->string(500)->comment('TAG: Comma-separated'), // comma-separated tags
            'view_count' => $this->integer()->defaultValue(0)->comment('จำนวนดู'),
            'sold_count' => $this->integer()->defaultValue(0)->comment('จำนวนขาย'),
            'is_featured' => $this->boolean()->notNull()->defaultValue(false)->comment('เลขอะไหล่แท้'),
            'is_active' => $this->boolean()->notNull()->defaultValue(true)->comment('เลขอะไหล่แท้'),
            'created_by' => $this->integer()->comment('สร้างโดย'),
            'updated_by' => $this->integer()->comment('แก้ไขโดย'),
            'created_at' => $this->integer()->notNull()->comment('สร้างเมื่อ'),
            'updated_at' => $this->integer()->notNull()->comment('แก้ไขเมื่อ'),
        ], $tableOptions);

        $this->addForeignKey('fk-part-category_id', '{{%part}}', 'category_id', '{{%part_category}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-part-supplier_id', '{{%part}}', 'supplier_id', '{{%supplier}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-part-created_by', '{{%part}}', 'created_by', '{{%users}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-part-updated_by', '{{%part}}', 'updated_by', '{{%users}}', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('idx-part-part_type', '{{%part}}', 'part_type');
        $this->createIndex('idx-part-is_active', '{{%part}}', 'is_active');
        $this->createIndex('idx-part-is_featured', '{{%part}}', 'is_featured');
        $this->createIndex('idx-part-oem_number', '{{%part}}', 'oem_number');

        // =====================================================
        // 8. ตาราง Part Vehicle Compatibility (ความเข้ากันได้กับรถ)
        // =====================================================
        $this->createTable('{{%part_vehicle}}', [
            'id' => $this->primaryKey(),
            'part_id' => $this->integer()->notNull(),
            'brand_id' => $this->integer()->notNull(),
            'model_id' => $this->integer(),
            'engine_type_id' => $this->integer(),
            'year_start' => $this->integer(),
            'year_end' => $this->integer(),
            'notes' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-part_vehicle-part_id', '{{%part_vehicle}}', 'part_id', '{{%part}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-part_vehicle-brand_id', '{{%part_vehicle}}', 'brand_id', '{{%vehicle_brand}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-part_vehicle-model_id', '{{%part_vehicle}}', 'model_id', '{{%vehicle_model}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-part_vehicle-engine_type_id', '{{%part_vehicle}}', 'engine_type_id', '{{%engine_type}}', 'id', 'SET NULL', 'CASCADE');

        // =====================================================
        // 9. ตาราง Customer (ลูกค้า)
        // =====================================================
        $this->createTable('{{%customer}}', [
            'id' => $this->primaryKey(),
            'customer_code' => $this->string(20)->notNull()->unique(),
            'customer_type' => $this->string(20)->notNull()->defaultValue('retail'), // retail, wholesale, garage
            'full_name' => $this->string(255)->notNull(),
            'company_name' => $this->string(255),
            'tax_id' => $this->string(20),
            'phone' => $this->string(20)->notNull(),
            'phone2' => $this->string(20),
            'email' => $this->string(255),
            'line_id' => $this->string(100),
            'facebook' => $this->string(255),
            'address' => $this->text(),
            'province' => $this->string(100),
            'district' => $this->string(100),
            'postal_code' => $this->string(10),
            'shipping_address' => $this->text(),
            'notes' => $this->text(),
            'credit_limit' => $this->decimal(12, 2)->defaultValue(0),
            'discount_percent' => $this->decimal(12, 1)->defaultValue(0),
            'total_purchases' => $this->decimal(12, 2)->defaultValue(0),
            'total_orders' => $this->integer()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-customer-customer_type', '{{%customer}}', 'customer_type');
        $this->createIndex('idx-customer-phone', '{{%customer}}', 'phone');

        // =====================================================
        // 10. ตาราง Customer Vehicle (รถของลูกค้า)
        // =====================================================
        $this->createTable('{{%customer_vehicle}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'brand_id' => $this->integer()->notNull(),
            'model_id' => $this->integer(),
            'engine_type_id' => $this->integer(),
            'year' => $this->integer(),
            'vin' => $this->string(17), // Vehicle Identification Number
            'license_plate' => $this->string(20),
            'color' => $this->string(50),
            'mileage' => $this->integer(),
            'notes' => $this->text(),
            'is_primary' => $this->boolean()->defaultValue(false),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-customer_vehicle-customer_id', '{{%customer_vehicle}}', 'customer_id', '{{%customer}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-customer_vehicle-brand_id', '{{%customer_vehicle}}', 'brand_id', '{{%vehicle_brand}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-customer_vehicle-model_id', '{{%customer_vehicle}}', 'model_id', '{{%vehicle_model}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-customer_vehicle-engine_type_id', '{{%customer_vehicle}}', 'engine_type_id', '{{%engine_type}}', 'id', 'SET NULL', 'CASCADE');

        // =====================================================
        // 11. ตาราง Order (คำสั่งซื้อ)
        // =====================================================
        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'order_number' => $this->string(20)->notNull()->unique(),
            'customer_id' => $this->integer()->notNull(),
            'order_date' => $this->date()->notNull(),
            'order_status' => $this->string(20)->notNull()->defaultValue('pending'),
            // pending, confirmed, preparing, shipped, delivered, cancelled
            'payment_status' => $this->string(20)->notNull()->defaultValue('unpaid'),
            // unpaid, partial, paid, refunded
            'payment_method' => $this->string(50), // transfer, cod, credit
            'subtotal' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'discount_amount' => $this->decimal(12, 2)->defaultValue(0),
            'discount_reason' => $this->string(255),
            'shipping_cost' => $this->decimal(12, 2)->defaultValue(0),
            'grand_total' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'shipping_method' => $this->string(50)->comment('วิธีจัดส่ง KEX, Flash, EMS, pickup'), // KEX, Flash, EMS, pickup
            'tracking_number' => $this->string(100)->comment('เลขพัสดุ'),
            'shipping_name' => $this->string(255)->comment('ชื่อผู้รับ'),
            'shipping_phone' => $this->string(20)->comment('เบอร์โทรผู้รับ'),
            'shipping_address' => $this->text()->comment('ที่อยู่จัดส่ง'),
            'customer_notes' => $this->text()->comment('หมายเหตุจากลูกค้า'),
            'internal_notes' => $this->text()->comment('หมายเหตุภายใน'),
            'shipped_at' => $this->integer()->comment('วันที่จัดส่ง'),
            'delivered_at' => $this->integer()->comment('วันที่ได้รับ'),
            'cancelled_at' => $this->integer()->comment('วันที่ยกเลิก'),
            'cancel_reason' => $this->string(255)->comment('เหตุผลยกเลิก'),
            'created_by' => $this->integer()->comment('สร้างโดย'),
            'updated_by' => $this->integer()->comment('แก้ไขโดย'),
            'created_at' => $this->integer()->notNull()->comment('วันที่สร้าง'),
            'updated_at' => $this->integer()->notNull()->comment('วันที่แก้ไข'),
        ], $tableOptions);

        $this->addForeignKey('fk-order-customer_id', '{{%order}}', 'customer_id', '{{%customer}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-order-created_by', '{{%order}}', 'created_by', '{{%users}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-order-updated_by', '{{%order}}', 'updated_by', '{{%users}}', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('idx-order-status', '{{%order}}', 'status');
        $this->createIndex('idx-order-payment_status', '{{%order}}', 'payment_status');
        $this->createIndex('idx-order-order_date', '{{%order}}', 'order_date');

        // =====================================================
        // 12. ตาราง Order Item (รายการสินค้าในคำสั่งซื้อ)
        // =====================================================
        $this->createTable('{{%order_item}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'part_id' => $this->integer()->notNull(),
            'part_name' => $this->string(255)->notNull(), // Snapshot
            'part_sku' => $this->string(50)->notNull(), // Snapshot
            'part_type' => $this->string(20)->notNull(), // Snapshot
            'unit_price' => $this->decimal(12, 2)->notNull(),
            'quantity' => $this->integer()->notNull()->defaultValue(1),
            'discount_percent' => $this->decimal(5, 2)->defaultValue(0),
            'discount_amount' => $this->decimal(12, 2)->defaultValue(0),
            'line_total' => $this->decimal(12, 2)->notNull(),
            'warranty_days' => $this->integer()->defaultValue(0),
            'warranty_expires_at' => $this->date(),
            'notes' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-order_item-order_id', '{{%order_item}}', 'order_id', '{{%order}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-order_item-part_id', '{{%order_item}}', 'part_id', '{{%part}}', 'id', 'RESTRICT', 'CASCADE');

        // =====================================================
        // 13. ตาราง Payment (การชำระเงิน)
        // =====================================================
        $this->createTable('{{%payment}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'payment_date' => $this->date()->notNull(),
            'amount' => $this->decimal(12, 2)->notNull(),
            'payment_method' => $this->string(50)->notNull(),
            'reference_number' => $this->string(100),
            'bank_name' => $this->string(100),
            'slip_image' => $this->string(255),
            'notes' => $this->text(),
            'verified_by' => $this->integer(),
            'verified_at' => $this->integer(),
            'payment_status' => $this->string(20)->notNull()->defaultValue('pending'),
            // pending, verified, rejected
            'created_by' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-payment-order_id', '{{%payment}}', 'order_id', '{{%order}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-payment-verified_by', '{{%payment}}', 'verified_by', '{{%users}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-payment-created_by', '{{%payment}}', 'created_by', '{{%users}}', 'id', 'SET NULL', 'CASCADE');

        // =====================================================
        // 14. ตาราง Inquiry (การสอบถาม/แชท)
        // =====================================================
        $this->createTable('{{%inquiry}}', [
            'id' => $this->primaryKey(),
            'inquiry_number' => $this->string(20)->notNull()->unique(),
            'customer_id' => $this->integer(),
            'customer_name' => $this->string(255),
            'customer_phone' => $this->string(20),
            'customer_line_id' => $this->string(100),
            'channel' => $this->string(20)->notNull(), // line, facebook, website, phone
            'status' => $this->string(20)->notNull()->defaultValue('open'),
            // open, in_progress, quoted, converted, closed
            'priority' => $this->string(10)->defaultValue('normal'), // low, normal, high
            'subject' => $this->string(255),
            'vehicle_info' => $this->text(), // JSON: brand, model, year, engine
            'requested_parts' => $this->text(), // JSON array of requested parts
            'quoted_amount' => $this->decimal(12, 2),
            'converted_order_id' => $this->integer(),
            'assigned_to' => $this->integer(),
            'notes' => $this->text(),
            'closed_at' => $this->integer(),
            'closed_reason' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-inquiry-customer_id', '{{%inquiry}}', 'customer_id', '{{%customer}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-inquiry-converted_order_id', '{{%inquiry}}', 'converted_order_id', '{{%order}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-inquiry-assigned_to', '{{%inquiry}}', 'assigned_to', '{{%users}}', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('idx-inquiry-status', '{{%inquiry}}', 'status');
        $this->createIndex('idx-inquiry-channel', '{{%inquiry}}', 'channel');

        // =====================================================
        // 15. ตาราง Inquiry Message (ข้อความในการสอบถาม)
        // =====================================================
        $this->createTable('{{%inquiry_message}}', [
            'id' => $this->primaryKey(),
            'inquiry_id' => $this->integer()->notNull(),
            'sender_type' => $this->string(20)->notNull(), // customer, staff, bot
            'sender_id' => $this->integer(), // user_id if staff
            'message' => $this->text()->notNull(),
            'attachments' => $this->text(), // JSON array of file paths
            'is_auto_reply' => $this->boolean()->defaultValue(false),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-inquiry_message-inquiry_id', '{{%inquiry_message}}', 'inquiry_id', '{{%inquiry}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-inquiry_message-sender_id', '{{%inquiry_message}}', 'sender_id', '{{%users}}', 'id', 'SET NULL', 'CASCADE');

        // =====================================================
        // 16. ตาราง Stock Movement (การเคลื่อนไหวสต็อก)
        // =====================================================
        $this->createTable('{{%stock_movement}}', [
            'id' => $this->primaryKey(),
            'part_id' => $this->integer()->notNull(),
            'movement_type' => $this->string(20)->notNull(),
            // in, out, adjustment, return, damaged
            'quantity' => $this->integer()->notNull(),
            'quantity_before' => $this->integer()->notNull(),
            'quantity_after' => $this->integer()->notNull(),
            'reference_type' => $this->string(50), // order, purchase, adjustment
            'reference_id' => $this->integer(),
            'unit_cost' => $this->decimal(12, 2),
            'notes' => $this->text(),
            'created_by' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-stock_movement-part_id', '{{%stock_movement}}', 'part_id', '{{%part}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-stock_movement-created_by', '{{%stock_movement}}', 'created_by', '{{%users}}', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('idx-stock_movement-movement_type', '{{%stock_movement}}', 'movement_type');
        $this->createIndex('idx-stock_movement-created_at', '{{%stock_movement}}', 'created_at');

        // // =====================================================
        // // 17. ตาราง Setting (การตั้งค่าระบบ)
        // // =====================================================
        // $this->createTable('{{%setting}}', [
        //     'id' => $this->primaryKey(),
        //     'category' => $this->string(50)->notNull(),
        //     'key' => $this->string(100)->notNull(),
        //     'value' => $this->text(),
        //     'type' => $this->string(20)->defaultValue('string'), // string, integer, boolean, json
        //     'description' => $this->string(255),
        //     'updated_by' => $this->integer(),
        //     'updated_at' => $this->integer()->notNull(),
        // ], $tableOptions);

        // $this->createIndex('idx-setting-category_key', '{{%setting}}', ['category', 'key'], true);

        // =====================================================
        // 18. ตาราง Activity Log (บันทึกกิจกรรม)
        // =====================================================
        $this->createTable('{{%activity_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'action' => $this->string(50)->notNull(),
            'model_class' => $this->string(100),
            'model_id' => $this->integer(),
            'description' => $this->text(),
            'old_values' => $this->text(), // JSON
            'new_values' => $this->text(), // JSON
            'ip_address' => $this->string(45),
            'user_agent' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-activity_log-user_id', '{{%activity_log}}', 'user_id', '{{%users}}', 'id', 'SET NULL', 'CASCADE');
        $this->createIndex('idx-activity_log-model', '{{%activity_log}}', ['model_class', 'model_id']);
        $this->createIndex('idx-activity_log-created_at', '{{%activity_log}}', 'created_at');

        // =====================================================
        // Insert Initial Data
        // =====================================================
        $this->insertInitialData();
    }

    private function insertInitialData()
    {
        $now = time();

        // Vehicle Brands
        $brands = [
            ['name_en' => 'Honda', 'name_th' => 'ฮอนด้า', 'country' => 'Japan', 'sort_order' => 1],
            ['name_en' => 'Toyota', 'name_th' => 'โตโยต้า', 'country' => 'Japan', 'sort_order' => 2],
            ['name_en' => 'Mercedes-Benz', 'name_th' => 'เมอร์เซเดส-เบนซ์', 'country' => 'Germany', 'sort_order' => 3],
            ['name_en' => 'BMW', 'name_th' => 'บีเอ็มดับเบิลยู', 'country' => 'Germany', 'sort_order' => 4],
            ['name_en' => 'Nissan', 'name_th' => 'นิสสัน', 'country' => 'Japan', 'sort_order' => 5],
            ['name_en' => 'Mazda', 'name_th' => 'มาสด้า', 'country' => 'Japan', 'sort_order' => 6],
            ['name_en' => 'Mitsubishi', 'name_th' => 'มิตซูบิชิ', 'country' => 'Japan', 'sort_order' => 7],
            ['name_en' => 'Isuzu', 'name_th' => 'อีซูซุ', 'country' => 'Japan', 'sort_order' => 8],
            ['name_en' => 'Ford', 'name_th' => 'ฟอร์ด', 'country' => 'USA', 'sort_order' => 9],
            ['name_en' => 'Chevrolet', 'name_th' => 'เชฟโรเลต', 'country' => 'USA', 'sort_order' => 10],
        ];

        foreach ($brands as $brand) {
            $this->insert('{{%vehicle_brand}}', array_merge($brand, [
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // Part Categories
        $categories = [
            ['name_en' => 'Engine', 'name_th' => 'ระบบเครื่องยนต์', 'slug' => 'engine', 'icon' => 'fa-cogs', 'sort_order' => 1],
            ['name_en' => 'Air Conditioning', 'name_th' => 'ระบบปรับอากาศ', 'slug' => 'air-conditioning', 'icon' => 'fa-snowflake', 'sort_order' => 2],
            ['name_en' => 'Suspension', 'name_th' => 'ช่วงล่าง', 'slug' => 'suspension', 'icon' => 'fa-car', 'sort_order' => 3],
            ['name_en' => 'Brake System', 'name_th' => 'ระบบเบรก', 'slug' => 'brake-system', 'icon' => 'fa-compact-disc', 'sort_order' => 4],
            ['name_en' => 'Electrical', 'name_th' => 'ระบบไฟฟ้า', 'slug' => 'electrical', 'icon' => 'fa-bolt', 'sort_order' => 5],
            ['name_en' => 'Body Parts', 'name_th' => 'ตัวถังและชิ้นส่วนภายนอก', 'slug' => 'body-parts', 'icon' => 'fa-door-closed', 'sort_order' => 6],
            ['name_en' => 'Interior', 'name_th' => 'ชิ้นส่วนภายใน', 'slug' => 'interior', 'icon' => 'fa-couch', 'sort_order' => 7],
            ['name_en' => 'Cooling System', 'name_th' => 'ระบบหล่อเย็น', 'slug' => 'cooling-system', 'icon' => 'fa-temperature-low', 'sort_order' => 8],
            ['name_en' => 'Fuel System', 'name_th' => 'ระบบน้ำมันเชื้อเพลิง', 'slug' => 'fuel-system', 'icon' => 'fa-gas-pump', 'sort_order' => 9],
            ['name_en' => 'Transmission', 'name_th' => 'ระบบส่งกำลัง', 'slug' => 'transmission', 'icon' => 'fa-exchange-alt', 'sort_order' => 10],
        ];

        foreach ($categories as $category) {
            $this->insert('{{%part_category}}', array_merge($category, [
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // Settings
        $settings = [
            ['setting_group ' => 'shop', 'setting_key ' => 'name', 'setting_value' => "Dune's Auto Parts", 'setting_type' => 'string', 'setting_description' => 'ชื่อร้าน'],
            ['setting_group ' => 'shop', 'setting_key ' => 'name_th', 'setting_value' => 'ดูน ออโต้ พาร์ท', 'setting_type' => 'string', 'setting_description' => 'ชื่อร้านภาษาไทย'],
            ['setting_group ' => 'shop', 'setting_key ' => 'phone', 'setting_value' => '02-XXX-XXXX', 'setting_type' => 'string', 'setting_description' => 'เบอร์โทรศัพท์'],
            ['setting_group ' => 'shop', 'setting_key ' => 'line_id', 'setting_value' => '@dunesautoparts', 'setting_type' => 'string', 'setting_description' => 'Line ID'],
            ['setting_group ' => 'shop', 'setting_key ' => 'email', 'setting_value' => 'info@dunesautoparts.com', 'setting_type' => 'string', 'setting_description' => 'อีเมล'],
            ['setting_group ' => 'shop', 'setting_key ' => 'open_time', 'setting_value' => '08:30', 'setting_type' => 'string', 'setting_description' => 'เวลาเปิดร้าน'],
            ['setting_group ' => 'shop', 'setting_key ' => 'close_time', 'setting_value' => '17:30', 'setting_type' => 'string', 'setting_description' => 'เวลาปิดร้าน'],
            ['setting_group ' => 'shipping', 'setting_key ' => 'cutoff_time', 'setting_value' => '14:00', 'setting_type' => 'string', 'setting_description' => 'เวลาตัดรอบส่งสินค้า'],
            ['setting_group ' => 'shipping', 'setting_key' => 'methods', 'setting_value' => '["Kerry","Flash","EMS","J&T"]', 'setting_type' => 'json', 'setting_description' => 'ช่องทางจัดส่ง'],
            ['setting_group ' => 'shipping', 'setting_key ' => 'cod_available', 'setting_value' => '1', 'setting_type' => 'boolean', 'setting_description' => 'รับเก็บเงินปลายทาง'],
            ['setting_group ' => 'warranty', 'setting_key ' => 'new_parts_days', 'setting_value' => '180', 'setting_type' => 'integer', 'setting_description' => 'ประกันอะไหล่ใหม่ (วัน)'],
            ['setting_group ' => 'warranty', 'setting_key ' => 'used_parts_days', 'setting_value' => '7', 'setting_type' => 'integer', 'setting_description' => 'ประกันอะไหล่มือสอง (วัน)'],
        ];

        foreach ($settings as $setting) {
            $this->insert('{{%system_settings}}', array_merge($setting, [
                'updated_at' => $now,
            ]));
        }

        // // Super Admin User
        // $this->insert('{{%users}}', [
        //     'username' => 'superadmin',
        //     'auth_key' => Yii::$app->security->generateRandomString(),
        //     'password_hash' => Yii::$app->security->generatePasswordHash('Superadmin@123!'),
        //     'email' => 'superadmin@dunesautoparts.com',
        //     'full_name' => 'Super Administrator',
        //     'role' => 'superadmin',
        //     'user_status' => 10,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);
             
        // // Admin User
        // $this->insert('{{%users}}', [
        //     'username' => 'admin',
        //     'auth_key' => Yii::$app->security->generateRandomString(),
        //     'password_hash' => Yii::$app->security->generatePasswordHash('Admin@123!'),
        //     'email' => 'admin@dunesautoparts.com',
        //     'full_name' => 'System Administrator',
        //     'role' => 'admin',
        //     'user_status' => 10,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);
    }

    public function safeDown()
    {
        $tables = [
            'activity_log',
            'setting',
            'stock_movement',
            'inquiry_message',
            'inquiry',
            'payment',
            'order_item',
            'order',
            'customer_vehicle',
            'customer',
            'part_vehicle',
            'part',
            'supplier',
            'part_category',
            'engine_type',
            'vehicle_model',
            'vehicle_brand',
            'user',
        ];

        foreach ($tables as $table) {
            $this->dropTable('{{%' . $table . '}}');
        }
    }
}
