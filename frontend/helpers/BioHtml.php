<?php

namespace biowareru\frontend\helpers;

use yii\helpers\Html;

class BioHtml extends Html
{
    public static function entDecode($string)
    {
        return htmlspecialchars_decode($string);
    }

}