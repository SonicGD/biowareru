<?php
/**
 * Created by PhpStorm.
 * User: sonic
 * Date: 13-Jun-15
 * Time: 18:12
 */

namespace biowareru\frontend\search\models;


use yii\sphinx\ActiveRecord;

class GamesSearch extends  ActiveRecord
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function indexName()
    {
        return 'be_games';
    }
}