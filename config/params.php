<?php

return [
    'ADMIN_EMAIL' => 'siddharth.k@spdynaics.net',
    'PATH_XML_EMAIL' => realpath(Yii::$app->basePath) . '/util/Xml/Email.xml',
    'PATH_XML_MESSAGES' => realpath(Yii::$app->basePath) . '/util/Xml/Messages.xml',
    'FORGOT_PASSWORD_EXPIRY_MINUTES'=>'15',
    'MCRYPT_SALT'=>'c0r3ap#p',
    //'DEVICE_TOKEN_OPTIONAL'=>FALSE, //FALSE in case of Push Notification else TRUE
    'FORGOT_PASSWORD_URL'=>'http://localhost/clarituscore/web/index.php/user/Api/v1/user/login',
    'UPLOAD_PATH'=>realpath(Yii::$app->basePath).'/uploads/',    
    'UPLOAD_URL'=>Yii::$app->request->baseUrl.'/uploads/',
    
    
    'SAMPLE' => realpath(Yii::$app->basePath) . '/util/Xml/Sample.xml',
];
