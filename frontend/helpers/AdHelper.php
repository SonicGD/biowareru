<?php

namespace biowareru\frontend\helpers;


use yii\db\Expression;

class AdHelper
{
    private static $ads = [];
    private static $library;

    public static $adsShowed = [];

    /**
     * @return advAdvertsLibrary
     */
    private static function getLibrary()
    {
        if (!self::$library) {
            $registry = \ipsRegistry::instance();
            $classToLoad = \IPSLib::loadLibrary(\IPSLib::getAppDir('advadverts') . '/sources/classes/library.php',
                'advAdvertsLibrary', 'advadverts');
            self::$library = new $classToLoad($registry);
        }
        return self::$library;
    }

    public static function getAds()
    {
        if (!self::$ads) {
            $advAdvertsLibrary = self::getLibrary();
            $cache_array = $advAdvertsLibrary->cache->getCache('adv_adverts');

            $adverts = $cache_array['mp_sidebar'];

            shuffle($adverts);
            self::setAds($adverts);

            register_shutdown_function(function () {
                \Yii::$app->db->createCommand()->update(\Yii::$app->db->tablePrefix . 'dp3_adv_adverts',
                    ['a_views' => new Expression('a_views + 1')],
                    ['a_id' => AdHelper::$adsShowed])->execute();
            });
        }

        return self::$ads;

    }

    public static function getAd()
    {
        \Yii::trace('Get ad');
        $ads = self::getAds();
        \Yii::trace('Pop ad');
        $ad = array_pop($ads);
        \Yii::trace('Set ads');
        self::setAds($ads);
        \Yii::trace('Update views');
        self::updateView($ad);
        \Yii::trace('Get library');
        $library = self::getLibrary();
        \Yii::trace('Make advert');
        $html = $library->makeAdvertImage($ad);
        $html = str_ireplace('index.php?/index.php?/index.php?/index.php', 'index.php', $html);
        \Yii::trace('Ad loaded');
        return $html;
    }

    public static function updateView($advert)
    {
        self::$adsShowed[] = $advert['a_id'];
    }

    private static function setAds($ads)
    {
        self::$ads = $ads;
    }
}