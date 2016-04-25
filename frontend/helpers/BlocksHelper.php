<?php

namespace biowareru\frontend\helpers;

use bioengine\common\modules\main\models\Block;

class BlocksHelper
{
    public static function getCounter()
    {
       return Block::findOne(['index' => 'counter']);
    }
}