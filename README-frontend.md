สรุปฟีเจอร์ที่สร้าง
ฟีเจอร์หน้า/ไฟล์🏠 หน้าแรกHero banner, สินค้าแนะนำ, หมวดหมู่, ยี่ห้อรถ🔍 ค้นหาอะไหล่ค้นหาตามชื่อ, รหัสอะไหล่, OEM📦 รายการสินค้ากรองหมวดหมู่, เรียงลำดับ, pagination🏷️ หมวดหมู่/ยี่ห้อดูสินค้าตามหมวดหมู่/ยี่ห้อรถ📄 รายละเอียดสินค้ารูป, ราคา, stock, สินค้าที่เกี่ยวข้อง🛒 ตะกร้าสินค้าเพิ่ม/ลบ/อัปเดตจำนวน, คำนวณราคา💳 ชำระเงินกรอกที่อยู่, เลือกวิธีชำระ✅ สั่งซื้อสำเร็จแสดงข้อมูลโอนเงิน/COD📋 ประวัติสั่งซื้อรายการออเดอร์, สถานะ🔎 ติดตามออเดอร์ค้นหาด้วยเลขออเดอร์+อีเมล👤 สมัคร/เข้าสู่ระบบระบบสมาชิกลูกค้า⚙️ โปรไฟล์แก้ไขข้อมูล, เปลี่ยนรหัสผ่าน📱 Responsiveรองรับ Mobile/Tablet/Desktop

โครงสร้างไฟล์ Frontend

frontend/
├── config/
│   ├── main.php
│   ├── params.php
│   └── bootstrap.php
├── controllers/
│   ├── SiteController.php
│   ├── PartController.php
│   ├── CartController.php
│   ├── OrderController.php
│   └── CustomerController.php
├── models/
│   ├── LoginForm.php
│   └── SignupForm.php
├── views/
│   ├── layouts/main.php
│   ├── site/ (index, about, contact, error)
│   ├── part/ (index, view, search, category, brand)
│   ├── cart/ (index)
│   ├── order/ (checkout, success, history, view, track)
│   └── customer/ (login, register, profile, change-password)
└── web/
    ├── index.php
    └── .htaccess

การติดตั้งเพิ่มเติม
หลัง migrate ใหม่ (เนื่องจาก customer table มีการเปลี่ยนแปลง):
bashphp yii migrate/fresh --interactive=0
ตั้งค่า Virtual Host สำหรับ frontend:

Document Root: frontend/web
URL: http://shop.dunes-autoparts.test (หรือตามต้องการ)