<?php
/**
 * Created by PhpStorm.
 * User: Георгий
 * Date: 07.07.2014
 * Time: 11:37
 */

use biowareru\frontend\helpers\AdHelper;
use biowareru\frontend\helpers\BioHtml;
use biowareru\frontend\helpers\ContentHelper;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

$config = [
    'id'                  => 'app-frontend',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'biowareru\frontend\controllers',
    'vendorPath'          => dirname(dirname(__DIR__)) . '/vendor',
    'layout'              => false,
    'components'          => [
        'cache'        => $params['components.cache'],
        'redis'        => $params['components.redis'],
        'feed'         => [
            'class' => \yii\feed\FeedDriver::class,
        ],
        'sphinx'       => [
            'class'    => 'yii\sphinx\Connection',
            'dsn'      => 'mysql:host=127.0.0.1;port=9306;',
            'username' => '',
            'password' => ''
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning']
                ]
            ]
        ],
        'errorHandler' => [
            'class'       => \biowareru\common\components\ErrorHandler::className(),
            'errorAction' => 'site/error'
        ],
        'db'           => $params['db'],
        'request'      => [
            'class'               => \yii\web\Request::className(),
            'cookieValidationKey' => 'somesecretvalidationkey',
            'parsers'             => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'view'         => [
            'renderers' => [
                'twig' => [
                    'class'     => \yii\twig\ViewRenderer::class,
                    // set cachePath to false in order to disable template caching
                    'cachePath' => '@runtime/Twig/cache',
                    // Array of twig options:
                    'options'   => [
                        'auto_reload' => true
                    ],
                    'globals'   => [
                        'production' => true,
                        'user'       => 'biowareru\frontend\helpers\UsersHelper',
                        'menu'       => 'biowareru\frontend\helpers\MenuHelper',
                        'slider'     => 'biowareru\frontend\helpers\SliderHelper',
                        'polls'      => 'biowareru\frontend\helpers\PollsHelper',
                        'html'       => '\yii\helpers\Html',
                        'bioHtml'    => BioHtml::class,
                        'content'    => ContentHelper::class,
                        'ads'        => AdHelper::class
                    ]
                    // ... see ViewRenderer for more options
                ]
            ]
        ]
    ],
    'params'              => $params
];

if (YII_DEBUG) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = ['class' => 'yii\debug\Module', 'allowedIPs' => ['127.0.0.1']];
}

return $config;