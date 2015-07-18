<?php

namespace biowareru\common\models;


use bioengine\common\components\BioActiveRecord;

/**
 * Class AdvAdverts
 * @package biowareru\common\models
 *
 * @property integer $a_id
 * @property integer $a_position
 * @property integer $a_enable
 * @property string  $a_name
 * @property string  $a_title
 * @property string  $a_link
 * @property integer $a_type
 * @property string  $a_image
 * @property integer  $a_section_id
 */
class AdvAdvert extends BioActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dp3_adv_adverts}}';
    }
}