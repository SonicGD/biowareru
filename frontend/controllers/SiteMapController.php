<?php

namespace biowareru\frontend\controllers;


use bioengine\common\modules\main\MainModule;
use bioengine\frontend\components\Controller;

class SiteMapController extends Controller
{

    public function actionGenerate()
    {
        $path = BW_ROOT . DIRECTORY_SEPARATOR . 'frontend/web';
        MainModule::generateSiteMap($path, \Yii::$app->params['site_url'] . '/');
    }
}