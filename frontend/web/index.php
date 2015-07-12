<?php

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
        if ($oldRequest !== $_SERVER['REQUEST_URI']) {
            sendToKato('Redirect from ' . $oldRequest . ' to ' . $_SERVER['REQUEST_URI']);
        }
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: https://www.bioware.ru' . $_SERVER['REQUEST_URI']);
        exit();
    }
}
if ($oldRequest !== $_SERVER['REQUEST_URI']) {
    sendToKato('Rename from ' . $oldRequest . ' to ' . $_SERVER['REQUEST_URI']);
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

function sendToKato($text, $color = 'red', $formatter = 'text')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HEADER, 'Content-Type: application/json; charset=utf-8\r\n');
    $obj = [
        'from'     => 'MY.CG',
        'color'    => $color,
        'renderer' => $formatter,
        'text'     => $text
    ];
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($obj));
    curl_setopt($ch, CURLOPT_URL,
        'https://api.kato.im/rooms/28e7979cc1026a68861c7a2e7d2138e4ef13da70e96b8078a3ae61744efb73e/simple');
    curl_exec($ch);
}

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