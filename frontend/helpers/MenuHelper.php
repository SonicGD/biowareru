<?php
/**
 * Created by PhpStorm.
 * User: sonic
 * Date: 5/3/2015
 * Time: 12:34 AM
 */

namespace biowareru\frontend\helpers;


use bioengine\common\modules\main\models\Menu;

class MenuHelper
{
    private static $menu = [];

    public static function getMenu()
    {

        if (!self::$menu) {
            /**
             * @var Menu $menu
             */
            $menu = Menu::find()->where(['key' => 'index'])->one();
            self::$menu = $menu->getItems();
        }

        return self::$menu;
    }

}