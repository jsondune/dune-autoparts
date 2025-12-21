<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'app-frontend',
    'name' => "Dune's Auto Parts",
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'language' => 'th',
    'timeZone' => 'Asia/Bangkok',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => 'frontend-secret-key-change-in-production',
        ],
        'user' => [
            'identityClass' => 'common\models\Customer',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl' => ['customer/login'],
        ],
        'session' => [
            'name' => 'frontend-session',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
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
                'about' => 'site/about',
                'contact' => 'site/contact',
                'search' => 'part/search',
                'parts' => 'part/index',
                'part/<id:\d+>' => 'part/view',
                'category/<id:\d+>' => 'part/category',
                'brand/<id:\d+>' => 'part/brand',
                'cart' => 'cart/index',
                'checkout' => 'order/checkout',
                'login' => 'customer/login',
                'register' => 'customer/register',
                'profile' => 'customer/profile',
                'orders' => 'order/history',
                'order/<id:\d+>' => 'order/view',
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap5\BootstrapAsset' => [
                    'css' => [],
                ],
            ],
        ],
    ],
    'params' => $params,
];
