<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'prod');
define('BIOENGINE_PATH', 'D:\Projects/bioengine');

$stdout = fopen('php://stdout', 'w');

function logerror($output)
{
    global $stdout;
    fwrite($stdout, print_r($output, true) . "\n"); //uncomment for phpunit realtime
}

require(__DIR__ . '/../../override.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/aliases.php');

$config = yii\helpers\ArrayHelper::merge(
    require(BIOENGINE_PATH . '/backend/config/main.php'),
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../config/main.php')
);

$application = new yii\web\Application($config);
Yii::setAlias('bower', dirname(dirname(__DIR__)) . '/vendor/bower');
Yii::setAlias('vendor', dirname(dirname(__DIR__)) . '/vendor');
$application->run();
