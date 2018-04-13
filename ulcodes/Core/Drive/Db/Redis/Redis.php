<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/11
 * Time: 16:41
 */

namespace ulcodes\Core\Drive\Db\Redis;


class Redis extends \Redis
{
    function __construct($host, $password, $port = 6379, $timeout = 30)
    {
        parent::__construct();
        parent::connect($host, $port, $timeout);
        parent::auth($password);
    }
}