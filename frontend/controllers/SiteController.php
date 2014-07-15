<?php

namespace biowareru\frontend\controllers;


class SiteController extends \bioengine\frontend\controllers\SiteController
{
    public function actionIndex()
    {
        \Yii::$app->name="123";
        return parent::actionIndex();
    }
} 