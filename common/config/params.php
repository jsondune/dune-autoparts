<?php
return [
    // Application Info
    'appName' => 'ดูน ออโต้ พาร์ท',
    'appNameEn' => "Dune's Auto Parts",
    'appVersion' => '1.0.0',
    'companyName' => 'ดูน ออโต้ พาร์ท',
    'companyPhone' => '02-XXX-XXXX',
    'companyEmail' => 'info@dunesautoparts.com',
    'companyAddress' => 'กรุงเทพมหานคร ประเทศไทย',
    
    // Support Email
    'supportEmail' => 'support@dunesautoparts.com',
    'adminEmail' => 'admin@dunesautoparts.com',
    'senderEmail' => 'noreply@dunesautoparts.com',
    'senderName' => 'ดูน ออโต้ พาร์ท',
    
    // User Settings
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 6,
    
    // Business Hours
    'businessHours' => [
        'open' => '08:30',
        'close' => '17:30',
        'shippingCutoff' => '14:00',
    ],
    
    // Stock Settings
    'stock.lowThreshold' => 5,
    'stock.criticalThreshold' => 2,
    
    // Order Settings
    'order.prefix' => 'SO',
    'order.maxItemsPerPage' => 20,
    
    // Customer Settings
    'customer.codePrefix' => 'CUST',
    
    // Part Settings
    'part.skuPrefix' => [
        'new_genuine' => 'NG',
        'new_oem' => 'NO',
        'used' => 'US',
    ],
    
    // Upload Settings
    'upload.maxFileSize' => 5 * 1024 * 1024, // 5MB
    'upload.allowedImageTypes' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    'upload.allowedDocTypes' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
    
    // Pagination
    'pagination.defaultPageSize' => 20,
    'pagination.pageSizeLimit' => [10, 20, 50, 100],
    
    // Thai Provinces (abbreviated list)
    'provinces' => [
        'กรุงเทพมหานคร',
        'นนทบุรี',
        'ปทุมธานี',
        'สมุทรปราการ',
        'สมุทรสาคร',
        'นครปฐม',
        'ชลบุรี',
        'ระยอง',
        'เชียงใหม่',
        'เชียงราย',
        'ภูเก็ต',
        'สงขลา',
        'นครราชสีมา',
        'ขอนแก่น',
        'อุดรธานี',
    ],
    
    // Shipping Methods
    'shippingMethods' => [
        'Kerry' => 'Kerry Express',
        'Flash' => 'Flash Express',
        'J&T' => 'J&T Express',
        'EMS' => 'ไปรษณีย์ EMS',
        'ลงทะเบียน' => 'ไปรษณีย์ลงทะเบียน',
        'รับเอง' => 'ลูกค้ารับเอง',
    ],
    
    // Payment Methods
    'paymentMethods' => [
        'bank_transfer' => 'โอนเงินผ่านธนาคาร',
        'cash' => 'เงินสด',
        'credit_card' => 'บัตรเครดิต',
        'promptpay' => 'พร้อมเพย์',
        'cod' => 'เก็บเงินปลายทาง',
    ],
    
    // Bank Accounts
    'bankAccounts' => [
        [
            'bank' => 'ธนาคารกสิกรไทย',
            'account_number' => 'XXX-X-XXXXX-X',
            'account_name' => 'บจก. ดูน ออโต้ พาร์ท',
        ],
        [
            'bank' => 'ธนาคารไทยพาณิชย์',
            'account_number' => 'XXX-X-XXXXX-X',
            'account_name' => 'บจก. ดูน ออโต้ พาร์ท',
        ],
    ],
    
    // Chat/Inquiry Settings
    'inquiry.channels' => [
        'line' => 'LINE',
        'facebook' => 'Facebook',
        'phone' => 'โทรศัพท์',
        'website' => 'เว็บไซต์',
        'walk_in' => 'หน้าร้าน',
        'other' => 'อื่นๆ',
    ],
    
    // AI Bot Settings
    'chatbot.enabled' => true,
    'chatbot.welcomeMessage' => 'สวัสดีครับ! ยินดีให้บริการครับ มีอะไรให้ช่วยครับ?',
    'chatbot.offlineMessage' => 'ขออภัยครับ ขณะนี้อยู่นอกเวลาทำการ กรุณาทิ้งข้อความไว้ เราจะติดต่อกลับโดยเร็วที่สุดครับ',
];
