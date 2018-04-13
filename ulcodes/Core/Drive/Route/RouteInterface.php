<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 15:07
 */

namespace ulcodes\Core\Drive\Route;


interface RouteInterface
{
    public function getControllerClassName();

    public function resolve();

    public function run();
}