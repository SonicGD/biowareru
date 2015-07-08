<?php

namespace biowareru\frontend\helpers;


use bioengine\common\helpers\UserHelper;
use bioengine\common\modules\ipb\models\IpbMember;
use yii\helpers\Url;

class UsersHelper
{
    /**
     * @return IpbMember
     */
    public static function getUser()
    {
        $user = UserHelper::getUser();
        return $user;
    }

    public static function __callStatic($name, array $params)
    {
        if ($user = self::getUser()) {
            return $user->$name;
        }

        return null;
    }

    public static function getLoginUrl()
    {
        return Url::toRoute(['site/login']);
    }
}

class BioWareMemeber extends IpbMember
{
    public $renegate = false;
}