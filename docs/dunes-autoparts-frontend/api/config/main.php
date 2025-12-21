<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'dunes-autoparts-api',
    'name' => "Dune's Auto Parts API",
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'language' => 'th',
    'timeZone' => 'Asia/Bangkok',
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => \yii\web\JsonParser::class,
            ],
            'enableCsrfValidation' => false,
        ],
        'response' => [
            'format' => \yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null && !isset($response->data['success'])) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                    ];
                }
            },
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/api.log',
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                // Chatbot API
                'POST chatbot/webhook/<channel:\w+>' => 'chatbot/webhook',
                'POST chatbot/message' => 'chatbot/message',
                'GET chatbot/products' => 'chatbot/products',
                'GET chatbot/product/<id:\d+>' => 'chatbot/product',
                'GET chatbot/order/<orderNumber:\w+>' => 'chatbot/order-status',
                
                // Parts API
                'GET parts' => 'part/index',
                'GET parts/<id:\d+>' => 'part/view',
                'GET parts/search' => 'part/search',
                'GET parts/categories' => 'part/categories',
                
                // Orders API
                'GET orders/<orderNumber:\w+>' => 'order/view',
                'POST orders' => 'order/create',
                
                // Customers API
                'GET customers/<code:\w+>' => 'customer/view',
                'POST customers' => 'customer/create',
                
                // Inquiries API
                'POST inquiries' => 'inquiry/create',
                'GET inquiries/<id:\d+>/messages' => 'inquiry/messages',
                'POST inquiries/<id:\d+>/messages' => 'inquiry/send-message',
                
                // Vehicles API
                'GET vehicles/brands' => 'vehicle/brands',
                'GET vehicles/models/<brandId:\d+>' => 'vehicle/models',
                
                // Health check
                'GET health' => 'site/health',
            ],
        ],
    ],
    'params' => $params,
];
