<?php

namespace biowareru\frontend\helpers;


class ShutDownHelper
{
    /**
     * @var callable[]
     */
    private static $functions = [];
    private static $registered = false;

    public static function addFunction(callable $func)
    {
        self::$functions[] = $func;
        if (!self::$registered) {
            register_shutdown_function(function () {
                ShutDownHelper::execute();
            });
            self::$registered = true;
        }
    }

    public static function execute()
    {
        if (!defined('YII_DEBUG') || !YII_DEBUG) {
            fastcgi_finish_request();
        }
        foreach (self::$functions as $func) {
            $func();
        }
    }
}