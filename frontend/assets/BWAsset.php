<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class BWAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/min/style_default.css'
    ];

    public $js = [
        'js/script.min.js'
    ];
}