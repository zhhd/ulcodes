<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/11
 * Time: 11:49
 */

namespace ulcodes\Core\Drive\Globals;


class Globals
{
    private static $_base_key = "ULCODES";

    public static function set($key, $value)
    {
        if (!isset($GLOBALS[self::$_base_key])) {
            $GLOBALS[self::$_base_key] = [];
        }
        $GLOBALS[self::$_base_key][$key] = $value;
    }

    public static function get($key)
    {
        if (!isset($GLOBALS[self::$_base_key][$key])) {
            return null;
        } else {
            return $GLOBALS[self::$_base_key][$key];
        }
    }
}