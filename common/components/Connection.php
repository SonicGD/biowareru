<?php
/**
 * Created by PhpStorm.
 * User: sonic
 * Date: 13-Jun-15
 * Time: 20:09
 */

namespace biowareru\common\components;


class Connection extends \yii\db\Connection
{
    public $dbname;
    public $sphinxPath;
}