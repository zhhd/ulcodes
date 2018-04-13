<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 11:50
 */

namespace ulcodes\Core\Drive\Route;


class Route
{
    public static function resolve()
    {

        $normalRoute = new NormalRoute();
        $normalRoute->resolve()->run();
    }
}