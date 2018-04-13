<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 12:15
 */

namespace ulcodes\Core\Drive\Debug;


class Debug
{
    private static $enabled = false;

    /**
     */
    public static function enable()
    {
        if (static::$enabled) {
            return;
        }

        static::$enabled = true;

        if (DEBUG) {
            error_reporting(-1);
            ini_set('display_errors', 1);
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 0);
        }
        ErrorHandle::register();
    }
}