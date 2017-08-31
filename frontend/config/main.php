<?php

$params = \yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id'        => 'frontend',
    'basePath'  => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'name' => 'Fintech',
    'homeUrl'   => '/',
    'components' => [
        'request' => [
            'baseUrl'    => '',
            'csrfParam'  => '_csrf-frontend',
            'csrfCookie' => [
                'httpOnly' => true,
            ]
        ],
        'session' => [
            'name' => 'frontend-session-id',
            'cookieParams' => [
                'httpOnly' => true,
                'path' => '/',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules' => [
                '/' => 'site/index',
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            'common\services\HeadlinesManagerInterface'       => 'common\services\HeadlinesManager',
            'common\services\HeadlinesPricesManagerInterface' => 'common\services\HeadlinesPricesManager',
        ],
    ],
    'params' => $params,
];
