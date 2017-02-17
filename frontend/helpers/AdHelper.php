<?php

namespace biowareru\frontend\helpers;


use biowareru\frontend\models\Advertisement;

class AdHelper
{
    /**
     * @var Advertisement[]
     */
    private static $ads = [];
    private static $library;

    public static $adsShowed = [];

    /**
     * @return Advertisement[]
     */
    public static function getAds()
    {
        if (!self::$ads) {
            /**
             * @var Advertisement[] $adverts
             */
            $adverts = Advertisement::find()->where(['ad_active' => 1, 'ad_location' => 'ad_sidebar'])->andWhere([
                'or',
                ['ad_end' => 0],
                ['>', 'ad_end', time()]
            ])->all();
            shuffle($adverts);
            self::setAds($adverts);

            /*ShutDownHelper::addFunction(function () {
                \Yii::$app->db->createCommand()->update(\Yii::$app->db->tablePrefix . 'dp3_adv_adverts',
                    ['a_views' => new Expression('a_views + 1')],
                    ['a_id' => AdHelper::$adsShowed])->execute();
            });*/
        }

        return self::$ads;

    }

    public static function getAd()
    {
        \Yii::trace('Get ad');
        $ads = self::getAds();
        \Yii::trace('Pop ad');
        /**
         * @var Advertisement $ad
         */
        $ad = array_pop($ads);
        \Yii::trace('Set ads');
        self::setAds($ads);
        \Yii::trace('Update views');
        //self::updateView($ad);

        \Yii::trace('Make advert');
        $htmlData = [
            'link'  => $ad->ad_link,
            'image' => 'https://uploads.bioware.ru/' . $ad->getImage()
        ];
        \Yii::trace('Ad loaded');
        return $htmlData;
    }

    public static function updateView($advert)
    {
        self::$adsShowed[] = $advert['a_id'];
    }

    /**
     * @param Advertisement[] $ads
     */
    private static function setAds($ads)
    {
        self::$ads = $ads;
    }
}