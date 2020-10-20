<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@uploadsDir' => '@app/uploads'
    ],
    'timeZone' => 'Asia/Manila',
    'components' => [
        'request' => [
            'cookieValidationKey' => 'WmN8T9HaAU1iRG21RxDFQdpJNie693wQ',
            'enableCsrfValidation' => false
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null) 
                {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'code' => $response->statusCode,
                        'data' => $response->data,
                    ];
                    
                    $response->statusCode = 200;
                }
            },
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                ]
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'user',
                    'extraPatterns' => [
                        'GET login' => 'login'
                    ],
                    'except' => ['delete']
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'server',
                    'extraPatterns' => [
                        'GET play' => 'play'
                    ],
                    'except' => ['delete']
                ]
            ],
        ],
    ],
    'params' => $params
];



if (YII_ENV_DEV)
{
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module'
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module'
    ];
}

return $config;
