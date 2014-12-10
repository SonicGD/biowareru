<?php
defined('BW_ROOT') or define("BW_ROOT", realpath(__DIR__ . '/../../'));
return [
    'db'                  => require_once('db.php'),
    'site_url'            => 'http://bw.localhost.ru',
    'ipbwi_BOARD_PATH'    => BW_ROOT . DIRECTORY_SEPARATOR . '/frontend/web/forum/',
    'ipbwi_ROOT_PATH'     => BW_ROOT . DIRECTORY_SEPARATOR . '/vendor/haslv/ipbwi/src/vendor/ipbwi/',
    'ipbwi_WEB_URL'       => 'http://bw.localhost.ru',
    'ipbwi_COOKIE_DOMAIN' => '.bw.localhost.ru',
];