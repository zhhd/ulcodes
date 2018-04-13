<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 14:37
 */

namespace ulcodes\Core\Drive\Log;


use ulcodes\Core\Drive\Config\Config;
use ulcodes\Core\Drive\Helper\Helper;

class Log implements LogInterface
{
    public static function debug($message)
    {
        $now     = Helper::now();
        $message = "[$now] Debug::$message\r";
        self::setLog($message);
    }

    public static function info($message)
    {
        $now     = Helper::now();
        $message = "[$now] Info::$message\r";
        self::setLog($message);
    }

    public static function error($message)
    {
        $now     = Helper::now();
        $message = "[$now] Error::$message\r";
        self::setLog($message);
    }

    private static function setLog($message)
    {
        if (Config::get('open_log')) {
            $now = Helper::now('Y-m-d');
            $dir = DIRECTORY_SEPARATOR;
            file_put_contents(BASE_DIR . "{$dir}~runtime{$dir}log{$dir}{$now}.txt", $message, FILE_APPEND);
        }
    }
}