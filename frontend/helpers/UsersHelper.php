<?php

namespace biowareru\frontend\helpers;


use bioengine\common\helpers\UserHelper;

class UsersHelper
{
    public static function getUser()
    {
        return UserHelper::getUser();
    }

    public static function __callStatic($name, array $params)
    {
        if ($user = self::getUser()) {
            return $user->$name;
        }

        return null;
    }
}