<?php

namespace biowareru\frontend\helpers;


use biowareru\common\models\AdvAdvert;
use yii\db\Expression;

class AdHelper
{
    /**
     * @var AdvAdvert[]
     */
    private static $ads = [];
    private static $library;

    public static $adsShowed = [];

    /**
     * @return \biowareru\common\models\AdvAdvert[]
     */
    public static function getAds()
    {
        if (!self::$ads) {
            /**
             * @var AdvAdvert[] $adverts
             */
            $adverts = AdvAdvert::find()->where(['a_enable' => 1, 'a_type' => 1, 'a_section_id' => 2])->all();
            shuffle($adverts);
            self::setAds($adverts);

            ShutDownHelper::addFunction(function () {
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
        /**
         * @var AdvAdvert $ad
         */
        $ad = array_pop($ads);
        \Yii::trace('Set ads');
        self::setAds($ads);
        \Yii::trace('Update views');
        self::updateView($ad);

        \Yii::trace('Make advert');
        $htmlData = [
            'link'  => '/forum/index.php?app=advadverts&module=core&section=main&do=redir&aid=' . $ad->a_id . '&appReferrer=',
            'image' => 'https://uploads.bioware.ru/banners/' . $ad->a_image
        ];
        \Yii::trace('Ad loaded');
        return $htmlData;
    }

    public static function updateView($advert)
    {
        self::$adsShowed[] = $advert['a_id'];
    }

    /**
     * @param AdvAdvert[] $ads
     */
    private static function setAds($ads)
    {
        self::$ads = $ads;
    }
}