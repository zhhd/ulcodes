<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 14:38
 */

namespace ulcodes\Core\Drive\Log;


interface LogInterface
{
    /**
     * 信息日志
     *
     * @param $message
     * @return mixed
     */
    public static function info($message);

    /**
     * 调试日志
     *
     * @param $message
     * @return mixed
     */
    public static function debug($message);


    /**
     * 错误日志
     *
     * @param $message
     * @return mixed
     */
    public static function error($message);
}