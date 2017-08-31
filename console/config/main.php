<?php

$params = \yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id'        => 'console',
    'basePath'  => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            'common\services\ParserInterface'                 => 'common\services\Parser',
            'common\services\HeadlinesManagerInterface'       => 'common\services\HeadlinesManager',
            'common\services\ReceiverInterface'               => 'common\services\Receiver',
            'common\services\FileConverterInterface'          => 'common\services\FileConverter',
            'common\services\FileGeneratorInterface'          => 'common\services\FileGenerator',
            'common\services\PriceReceiverInterface'          => 'common\services\PriceReceiver',
            'common\services\HeadlinesPricesManagerInterface' => 'common\services\HeadlinesPricesManager',
        ],
        'singletons' => [
            'common\services\ParserInterface' => 'common\services\Parser',
        ]
    ],
    'params' => $params,
];
