<?php
/**
 * Created by PhpStorm.
 * User: Георгий
 * Date: 07.07.2014
 * Time: 11:37
 */

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    //'bootstrap'  => ['debug'],
    'components' => [
        'db'    => $params['db'],
        'redis' => $params['components.redis'],
        'cache' => $params['components.cache']
    ],
    'modules'    => [
        //  'debug' => 'yii\debug\Module'
    ],
    'params'     => $params
];