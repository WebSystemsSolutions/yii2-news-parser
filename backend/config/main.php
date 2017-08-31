<?php

$params = \yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id'        => 'backend',
    'basePath'  => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'name' => 'Fintech',
    'components' => [
        'request' => [
            'csrfParam'  => '_csrf-backend',
            'baseUrl'    => '/back-office',
            'csrfCookie' => [
                'httpOnly' => true,
            ]
        ],
        'user' => [
            'identityClass'       => 'backend\models\User',
            'enableAutoLogin'     => true,
            'identityCookie'      => ['name' => '_identity-backend', 'httpOnly' => true],
            'authTimeout'         => 60 * 60,      //60 minutes
            'absoluteAuthTimeout' => 60 * 60 * 24, //24 hours
        ],
        'session' => [
            'name' => 'backend-session-id',
            'cookieParams' => [
                'httpOnly' => true,
                'path'     => '/back-office',
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
                '/'                  => 'site/index',
                'articles'           => 'headlines/index',
                'prices'             => 'headlines-price/index',
                'companies'          => 'headlines-company/index',
                'articles/<action>'  => 'headlines/<action>',
                'prices/<action>'    => 'headlines-price/<action>',
                'companies/<action>' => 'headlines-company/<action>',
            ],
        ],
    ],
    'modules' => [
        'redactor' => [
            'class' => 'yii\redactor\RedactorModule',
        ],
    ],
    'container' => [
        'definitions' => [
            'common\services\ParserInterface'                  => 'common\services\Parser',
            'common\services\HeadlinesManagerInterface'        => 'common\services\HeadlinesManager',
            'common\services\ReceiverInterface'                => 'common\services\Receiver',
            'common\services\PriceReceiverInterface'           => 'common\services\PriceReceiver',
            'common\services\HeadlinesPricesManagerInterface'  => 'common\services\HeadlinesPricesManager',
            'common\services\FileConverterInterface'           => 'common\services\FileConverter',
            'common\services\FileGeneratorInterface'           => 'common\services\FileGenerator',
            'common\services\HeadlinesCompanyManagerInterface' => 'common\services\HeadlinesCompanyManager',
            'backend\services\ArchiveInterface'                => 'backend\services\Archive',
            'backend\services\FileImportInterface'             => 'backend\services\FileImportCsv',
        ],
        'singletons' => [
            'common\services\ParserInterface' => 'common\services\Parser',
        ]
    ],
    'params' => $params,
];
