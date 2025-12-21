<?php

use yii\db\Migration;

/**
 * User Table Migration
 * with Authentication และ Security Features
 */
class m130524_201442_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function Up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // ปิดการเช็ค Foreign Key
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey()->comment('รหัสผู้ใช้'),
            'username' => $this->string(50)->notNull()->unique()->comment('ชื่อผู้ใช้'),
            'auth_key' => $this->string(32)->notNull()->comment('Authentication key'),			
            'password_hash' => $this->string(255)->notNull()->unique()->comment('รหัสผ่านเข้ารหัส'),
            'password_reset_token' => $this->string(255)->null()->unique()->comment('Token รีเซ็ตรหัสผ่าน'),			
            'email_address' => $this->string(100)->notNull()->comment('อีเมล'),			
			
			// Profile Information
            'title_name' => $this->string(100)->notNull()->comment('คำนำหน้า'),            
            'first_name' => $this->string(100)->notNull()->comment('ชื่อ'),
            'middle_name' => $this->string(100)->notNull()->comment('ชื่อกลาง'),               
            'last_name' => $this->string(100)->notNull()->comment('นามสกุล'),
            'title_name_en' => $this->string(100)->notNull()->comment('Title'),             
            'first_name_en' => $this->string(100)->notNull()->comment('First name'),
            'middle_name_en' => $this->string(100)->notNull()->comment('Middle name'),              
            'last_name_en' => $this->string(100)->notNull()->comment('Last name'),
            'organization_id' => $this->integer(10)->null()->comment('หน่วยงาน'),            
            'phone_number' => $this->string(50)->comment('โทรศัพท์'),		
            'line_id' => $this->string(100)->comment('Line ID'),		                                    
            'position_name' => $this->string(255)->null()->comment('ตำแหน่ง'),	
            'department' => $this->string(100)->null()->comment('แผนก/ฝ่าย'),
			'avatar_file_path' => $this->string(255)->null()->comment('รูปโปรไฟล์'),
            'oauth_provider' => $this->string(50)->null()->comment('OAuth Provider'),
            'azure_object_id' => $this->string(255)->null()->comment('Microsoft Entra ID (Object ID)'),
            'azure_upn' => $this->string(100)->null()->comment('Azure UPN'),
            'azure_synced_at' => $this->datetime()->null()->comment('Azure Sync Time'),     
			            
			// Security
			'failed_login_attempts' => $this->integer(2)->notNull()->defaultValue(0)->comment('จำนวนครั้งที่ Login ผิดพลาด'),
            'locked_until' => $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP')->comment('ล็อคบัญชีจนถึงเวลา'),
            'last_login_at' => $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP')->comment('เข้าสู่ระบบครั้งล่าสุด'),
            'last_login_ip' => $this->string(45)->null()->comment('IP ล่าสุด'),
            
			// Email Verification
            'verification_token' => $this->string(255)->null()->comment('Token ยืนยันอีเมล'),           
			'email_verified_at' => $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP')->comment('วันที่ยืนยันอีเมล'),

			// Status
            'user_status' => $this->smallInteger()->null()->defaultValue(10)->comment('สถานะ: 0=ลบ, 9=ไม่ใช้งาน, 10=ใช้งาน'), 
            
			// Timestamps
			'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('สร้างเมื่อ'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('แก้ไขเมื่อ'),
            'created_by' => $this->integer()->null()->comment('สร้างโดย'),
            'updated_by' => $this->integer()->null()->comment('แก้ไขโดย')            
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT="ข้อมูลผู้ใช้งาน"');
        
        // Create indexes
        $this->createIndex('uk_users_username', '{{%users}}', 'username', true);
        $this->createIndex('uk_users_email', '{{%users}}', 'email_address', true);
        $this->createIndex('uk_users_password_reset_token', '{{%users}}', 'password_reset_token', true);
        $this->createIndex('idx_users_organization', '{{%users}}', 'organization_id');
        $this->createIndex('idx_users_status', '{{%users}}', 'user_status');
        $this->createIndex('idx_users_name', '{{%users}}', ['first_name', 'last_name']);
        $this->createIndex('idx_users_auth_key', '{{%users}}', 'auth_key');

        // Create default super admin user
        $this->insert('{{%users}}', [
            'username' => 'admin',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('Admin@123!'),
            'email' => 'admin@pbri.ac.th',
            'fullname' => 'System Administrator',
            'fullname_th' => 'ผู้ดูแลระบบ',
            'status' => 10, // STATUS_ACTIVE
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        echo "    > User table created with default admin user.\n";
        echo "    > Username: admin\n";
        echo "    > Password: Admin@123!\n";
        echo "    > ⚠️  Please change the admin password immediately!\n";        
		
        // เปิดการเช็ค Foreign Key คืน
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');        
		
		return true;		
    }

    public function down()
    {
        $this->dropTable('{{%users}}');
    }
}
