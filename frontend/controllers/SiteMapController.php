<?php
/**
 * Created by PhpStorm.
 * User: sonic
 * Date: 09-Jul-15
 * Time: 10:03
 */

namespace biowareru\frontend\controllers;


use bioengine\common\modules\main\MainModule;
use bioengine\frontend\components\Controller;

class SiteMapController extends Controller
{

    public function actionGenerate()
    {
        $path = BW_ROOT . DIRECTORY_SEPARATOR . 'frontend/web/sitemap.xml';
        MainModule::generateSiteMap($path);
    }
}