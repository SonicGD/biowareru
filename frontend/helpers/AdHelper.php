<?php

namespace biowareru\frontend\helpers;


class AdHelper
{
    private static $ads = [];
    private static $library;

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
        }
        return self::$ads;

    }

    public static function getAd()
    {
        $ads = self::getAds();

        $ad = array_pop($ads);
        self::setAds($ads);

        self::updateView($ad);

        $library = self::getLibrary();
        $html = $library->makeAdvertImage($ad);

        return $html;
    }

    public static function updateView($advert)
    {
        $library = self::getLibrary();
        $library->DB->update('dp3_adv_adverts', 'a_views = a_views + 1', 'a_id = ' . $advert['a_id'], false, true);
    }

    private static function setAds($ads)
    {
        self::$ads = $ads;
    }
}