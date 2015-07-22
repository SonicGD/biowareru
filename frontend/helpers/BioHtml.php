<?php

namespace biowareru\frontend\helpers;

use yii\helpers\Html;
use yii\helpers\Url;

class BioHtml extends Html
{
    public static function entDecode($string)
    {
        return htmlspecialchars_decode($string);
    }

    public static function currentUrl()
    {
        return Url::current();
    }

}