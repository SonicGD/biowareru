<?php
return [
    'class'             => \yii\db\Connection::className(),
    'dsn'               => 'mysql:host=localhost;dbname=mydbname',
    'username'          => 'mysql_user',
    'password'          => 'mysql_password',
    'charset'           => 'utf8',
    'enableSchemaCache' => true,
    'tablePrefix'       => 'pre_'
];