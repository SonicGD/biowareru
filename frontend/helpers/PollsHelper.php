<?php

namespace biowareru\frontend\helpers;


use bioengine\common\modules\polls\models\Poll;

class PollsHelper
{
    private static $currentPoll;

    public static function current()
    {
        if (!self::$currentPoll) {
            self::$currentPoll = Poll::getCurrent();
        }

        return self::$currentPoll;
    }

}