<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'prod');
$path = '';
switch (strtoupper(substr(PHP_OS, 0, 3))) {
    case 'WIN':
        $path = __DIR__ . '/../../../bioengine';
        break;
    case 'CYG':
        $path = __DIR__ . '/../../../bioengine';
        break;
    default:
        $path = __DIR__ . '/../../vendor/sonicgd/bioengine';
        break;
}
define('BIOENGINE_PATH', $path);

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

$application = new \bioengine\common\BioEngine($config);
Yii::setAlias('bower', dirname(dirname(__DIR__)) . '/vendor/bower');
Yii::setAlias('vendor', dirname(dirname(__DIR__)) . '/vendor');
require(__DIR__ . '/../../vendor/sonicgd/bioengine/common/config/ipbwi.config.php');
$application->run();
