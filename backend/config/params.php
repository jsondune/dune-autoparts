<?php
return [
    'adminEmail' => 'admin@dunesautoparts.com',
    
    // Dashboard Settings
    'dashboard.recentOrdersLimit' => 5,
    'dashboard.lowStockLimit' => 10,
    'dashboard.recentInquiriesLimit' => 5,
    'dashboard.topSellingLimit' => 5,
    'dashboard.salesChartDays' => 7,
    
    // Listing Settings
    'list.pageSize' => 20,
    'list.pageSizeOptions' => [10, 20, 50, 100],
    
    // Upload Paths (relative to @webroot)
    'uploadPath' => [
        'parts' => 'uploads/parts',
        'payments' => 'uploads/payments',
        'avatars' => 'uploads/avatars',
        'inquiry' => 'uploads/inquiry',
        'documents' => 'uploads/documents',
    ],
    
    // Export Settings
    'export.maxRows' => 10000,
    'export.defaultFormat' => 'csv',
];
