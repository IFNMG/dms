<?php

$params = require(__DIR__ . '/params.php');

$config = [  
    'language' => 'en', // english
    //'sourceLanguage' => 'en-US',
    
    
   
    'id'=>'Clarituscore',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log',
        'app\base\Configurations',                 
                    
        ],
    'defaultRoute' => 'adminuser/admin/login',
    
    'components' => [
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource'
                ],
            ],
        ],
        
        
         'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.claritusconsulting.com',
                'username' => 'siddharth.k@spdynaics.net',
                'password' => 'Mithi@123',
                'port' => '25',
                'encryption' => 'tls',
            ],
            'useFileTransport' => false,
        ],
        
        
        
        
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'LOt51DfgSi9cUrbe5bf-xxxxx',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'admin' => [
            'class' => 'app\facades\adminuser\AdminFacade',
            //'enableAutoLogin' => true,
//            'loginUrl' => ['user/login'],
        ],
        'apiuser' => [
            'class' => 'app\modules\user\facades\UserFacade',
            //'enableAutoLogin' => true,
//            'loginUrl' => ['user/login'],
        ],    
        'errorHandler' => [
            'errorAction' =>'adminuser/admin/error',
        ],
        
        'smtp' => 'yii\smtp\Mail',
        'ses' => 'yii\ses\Mailer',
        'thread' => 'yii\thread\Threads',
        'thread1' => 'yii\thread1\Multiple',
        'thread2' => 'yii\thread1\Task\Sample',
        
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'maxFileSize' => 1024 * 2,
                    'logFile'=>'@runtime/logs/app_'.date('Y-m-d').'.log'
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'adminuser/admin/resetpassword/<id>' => 'adminuser/admin/resetpassword',
                'adminuser/admin/edituserprofile/<id>' => 'adminuser/admin/edituserprofile',
                'adminuser/admin/viewuserprofile/<id>' => 'adminuser/admin/viewuserprofile',
                'adminuser/admin/profile/<id>' => 'adminuser/admin/profile',
                'adminuser/core/pages/<cat>/<url>' => 'adminuser/core/pages',
                'adminuser/core/pages/<url>' => 'adminuser/core/pages',
            ],
        ],
        
        
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => true,
            'loginUrl' => ['index.php/adminuser/admin/login'],  
        ],
        
             
        //'authManager' => [
        //    'class' => 'yii\rbac\PhpManager',
        //],

    ],
    'params' => $params,
    
     'modules' => [
        'user' => [
            'class' => 'app\modules\user\Module',
        ],
         
        'navitrex' => [
            'class' => 'app\modules\navitrex\Module',
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    /*$config['bootstrap'][] = 'debug';*/
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',       
    ];
}

return $config;
