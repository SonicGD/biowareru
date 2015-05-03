<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'prod');
define('BIOENGINE_PATH', 'D:\Projects/bioengine');

require(__DIR__ . '/../../override.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/aliases.php');

$config = yii\helpers\ArrayHelper::merge(
    require(BIOENGINE_PATH . '/frontend/config/main.php'),
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../config/main.php')
);

$application = new \bioengine\common\BioEngine($config);
require(__DIR__ . '/../../vendor/sonicgd/bioengine/common/config/ipbwi.config.php');
$application->run();
