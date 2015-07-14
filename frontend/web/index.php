<?php

$debug = false;
if (isset($_GET['enableDebugPlease']) && $_GET['enableDebugPlease'] == 42) {
    setcookie('bwdebug', 1);
    $debug = true;
}
if (isset($_COOKIE['bwdebug'])) {
    $debug = true;
}

if ($debug) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');
}

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
$oldRequest = $_SERVER['REQUEST_URI'];
if ($_SERVER['REQUEST_URI'] !== '/') {
    $_SERVER['REQUEST_URI'] = '/' . trim($_SERVER['REQUEST_URI'], '/');
}
$_SERVER['REQUEST_URI'] = str_ireplace('.xml', '.html', $_SERVER['REQUEST_URI']);
$url = parse_url($_SERVER['REQUEST_URI']);
if ($url['path'] !== '/' && stripos($url['path'], '.html') === false) {
    $url['path'] .= '.html';
    $_SERVER['REQUEST_URI'] = unparse_url($url);
    if (stripos($url['path'], 'rss') === false && stripos($url['path'], 'thumb') === false) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: https://www.bioware.ru' . $_SERVER['REQUEST_URI']);
        exit();
    }
}

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

function unparse_url($parsed_url)
{
    $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
    $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
    $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
    $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
    $pass = ($user || $pass) ? "$pass@" : '';
    $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
    $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
    return "$scheme$user$pass$host$port$path$query$fragment";
}