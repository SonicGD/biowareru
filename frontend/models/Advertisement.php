<?php

namespace biowareru\frontend\models;


use bioengine\common\components\BioActiveRecord;

/**
 * Class Advertisement
 * @package biowareru\frontend\models
 * @property integer $ad_id
 * @property string  $ad_location
 * @property string  $ad_html
 * @property string  $ad_images
 * @property string  $ad_link
 * @property integer $ad_impressions
 * @property integer $ad_clicks
 * @property string  $ad_exempt
 * @property integer $ad_active
 * @property string  $ad_html_https
 * @property integer $ad_start
 * @property integer $ad_end
 * @property integer $ad_maximum_value
 * @property string  $ad_maximum_unit
 * @property string  $ad_additional_settings
 * @property integer $ad_html_https_set
 * @property integer $ad_member
 * @property integer $ad_new_window
 */
class Advertisement extends BioActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%core_advertisements}}';
    }

    public function getImage($size = 'large')
    {
        $images = json_decode($this->ad_images, true);
        return $images[$size]??null;
    }
}