<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'CNH9YDTZBtpUAWPEs52DDjI4DF2IDm18',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'load/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            //'useFileTransport' => true,
            'useFileTransport' => false,
            'transport'=>[
                'class'=>'Swift_SmtpTransport',
                'host'=>'smtp.163.com',
                'username'=>'13535413258@163.com',
                'password'=>'3651842',
                'port'=>'465',
                'encryption'=>'ssl',
            ],
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
        'aws' => [
            'class' => 'app\components\Aws',
            'accessKeyId' => 'AKIAILWFAYXTE72526SA',
            'secretAccessKey' => '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',
        ],
        'db' => require(__DIR__ . '/db.php'),
        //连接2个不同的数据库
        'remote_db' => require(__DIR__ . '/remote_db.php'),
        
        'urlManager' => [             
            'enablePrettyUrl' => false,
            'showScriptName' => false,
            'rules' => [              
            ],                        
        ],
        
    ],
    'params' => $params,
    //添加设置语言中文
    'language'=>'zh-CN',
    //设置时区
    'timezone'=>'Asia/Shanghai',
    'defaultRoute' => 'redshift/index',
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*','127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    $config['modules']['admin']=[
        'class'=>'app\modules\admin'
    ];
}

return $config;
