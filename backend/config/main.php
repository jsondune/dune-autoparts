<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'dunes-autoparts-backend',
    'name' => 'ดูน ออโต้ พาร์ท - ระบบจัดการ',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'language' => 'th',
    'sourceLanguage' => 'th',
    'timeZone' => 'Asia/Bangkok',
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => 'your-secret-key-change-this',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'loginUrl' => ['site/login'],
        ],
        'session' => [
            'name' => 'dunes-autoparts-backend',
            'cookieParams' => [
                'httpOnly' => true,
                'secure' => false, // Set to true in production with HTTPS
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'login' => 'site/login',
                'logout' => 'site/logout',
                
                // Part routes
                'parts' => 'part/index',
                'parts/low-stock' => 'part/low-stock',
                'parts/stock-history' => 'part/stock-history',
                'part/<id:\d+>' => 'part/view',
                
                // Order routes
                'orders' => 'order/index',
                'order/<id:\d+>' => 'order/view',
                'order/<id:\d+>/print' => 'order/print',
                
                // Customer routes
                'customers' => 'customer/index',
                'customer/<id:\d+>' => 'customer/view',
                
                // Inquiry routes
                'inquiries' => 'inquiry/index',
                'inquiry/<id:\d+>' => 'inquiry/view',
                
                // Report routes
                'reports/sales' => 'report/sales',
                'reports/inventory' => 'report/inventory',
                'reports/top-products' => 'report/top-products',
                'reports/top-customers' => 'report/top-customers',
                'reports/export-sales' => 'report/export-sales',
                'reports/export-inventory' => 'report/export-inventory',
                
                // User routes
                'users' => 'user/index',
                'user/<id:\d+>' => 'user/view',
                'profile' => 'user/profile',
                
                // Settings
                'settings' => 'setting/index',
                
                // Standard CRUD routes
                '<controller:\w+>' => '<controller>/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap5\BootstrapAsset' => [
                    'css' => [],
                ],
                'yii\bootstrap5\BootstrapPluginAsset' => [
                    'js' => [],
                ],
            ],
        ],
    ],
    'params' => $params,
];
