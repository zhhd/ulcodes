<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 14:02
 */

namespace ulcodes\Core\Drive\Helper;


use ulcodes\Core\Drive\Config\Config;
use ulcodes\Core\Drive\Db\Mysql\Mysql;
use ulcodes\Core\Drive\Db\Redis\Redis;
use ulcodes\Core\Drive\Globals\Globals;
use ulcodes\Core\Drive\Session\Session;
use ulcodes\Core\Drive\Session\SessionRedis;

class Helper
{
    private static $_pool = [];

    /**
     * 当前时间
     *
     * @param string $format
     * @return false|string
     */
    public static function now($format = 'Y-m-d H:i:s')
    {
        return date($format, time());
    }

    /**
     * json_encode
     *
     * @param     $value
     * @param int $options
     * @return string
     */
    public static function json($value, $options = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($value, $options);
    }

    /**
     * include
     * @param $file
     */
    public static function includeFile($file)
    {
        include $file;
    }

    /**
     * post获取
     *
     * @param null $name
     * @return mixed|null
     */
    public static function post($name = null)
    {
        $post = Globals::get('post');
        return is_null($name) ? $post : (isset($post[$name]) ? $post[$name] : null);
    }

    /**
     * get获取
     *
     * @param $name
     * @return mixed|null
     */
    public static function get($name)
    {
        $get = Globals::get('get');
        return is_null($name) ? $get : (isset($get[$name]) ? $get[$name] : null);
    }

    /**
     * input获取
     *
     * @param $name
     * @return null
     */
    public static function input($name)
    {
        $input = Globals::get('input');
        return is_null($name) ? $input : (isset($input[$name]) ? $input[$name] : null);
    }

    /**
     * 判断是否为https
     *
     * @return bool
     */
    public static function isSsl()
    {
        if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
            return true;
        } elseif (isset($_SERVER['REQUEST_SCHEME']) && 'https' == $_SERVER['REQUEST_SCHEME']) {
            return true;
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO']) {
            return true;
        }
        return false;
    }

    /**
     * 获取客户端IP地址,负载均衡请使用高级模式获取
     *
     * @param int  $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param bool $adv  是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public static function ip($type = 0, $adv = false)
    {
        $type = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim(current($arr));
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];

        return $ip[$type];
    }

    /**
     * 获取当前项目路径
     *
     * @return string
     */
    public static function getCurrentObjectUrl()
    {
        return (self::isSsl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    }

    /**
     * 获取当前url
     *
     * @return string
     */
    public static function getCurrentUrl()
    {
        return (self::isSsl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }


    /**
     * redis 连接
     *
     * @param string $name 配置文件中对应的redis键
     * @return Redis
     */
    public static function redis($name = 'default')
    {
        $config = Config::get('redis')[$name];
        $md5    = md5(self::json($config));
        if (!isset(static::$_pool['redis'])) {
            static::$_pool['redis'] = [];
        }
        if (isset(static::$_pool['redis'][$md5])) {
            return static::$_pool['redis'][$md5];
        } else {
            $redis = new Redis($config['host'], $config['password'],
                $config['port'], $config['timeout']);
            $redis->select($config['dbindex']);
            static::$_pool['redis'][$md5] = $redis;
        }
        return $redis;
    }

    /**
     * session 使用
     *
     * @param null $key
     * @param null $value
     * @return bool|mixed|null
     */
    public static function session($key = null, $value = null)
    {
        $session_model  = Config::get('session_model');
        $session_expire = Config::get('session_expire');
        $md5            = md5($session_model);
        if (!isset(static::$_pool['session'])) {
            static::$_pool['session'] = [];
        }
        if (isset(static::$_pool['session'][$md5])) {
            $session = static::$_pool['session'][$md5];
        } else {
            if ($session_model == 1) {
                $session = new Session();
            } else {
                $session = new SessionRedis(self::redis('session'));
            }
            $session->expire($session_expire);
            static::$_pool['session'][$md5] = $session;
        }

        if ($key == null) {
            return $session->get();
        } elseif ($value == null) {
            return $session->get($key);
        } else {
            $session->set($key, $value);
            return true;
        }
    }

    /**
     * 清理session
     *
     * @param string $key
     */
    public static function session_delete($key = '*')
    {
        $session_model  = Config::get('session_model');
        $session_expire = Config::get('session_expire');
        $md5            = md5($session_model);
        if (!isset(static::$_pool['session'])) {
            static::$_pool['session'] = [];
        }
        if (isset(static::$_pool['session'][$md5])) {
            $session = static::$_pool['session'][$md5];
        } else {
            if ($session_model == 1) {
                $session = new Session();
            } else {
                $session = new SessionRedis(self::redis('session'));
            }
            $session->expire($session_expire);
            static::$_pool['session'][$md5] = $session;
        }

        $session->delete($key);
    }

    /**
     * mysql 连接
     *
     * @param string $name
     * @return Mysql
     */
    public static function mysql($name = 'default')
    {
        $config = Config::get('mysql')[$name];
        $key    = md5(self::json($config));
        if (!isset(static::$_pool['mysql'])) {
            static::$_pool['mysql'] = [];
        }
        if (isset(static::$_pool['mysql'][$key])) {
            return static::$_pool['mysql'][$key];
        } else {
            $mysql                        = new Mysql($config['hostname'], $config['hostport'],
                $config['username'], $config['password'], $config['database'], $config['charset']);
            static::$_pool['mysql'][$key] = $mysql;
        }

        static::$_pool['mysql']['last_connect'] = $mysql;
        return $mysql;
    }

    /**
     * 最后一次mysql
     *
     * @return null|Mysql
     */
    public static function last_mysql()
    {
        if (isset(static::$_pool['mysql']['last_connect'])) {
            return static::$_pool['mysql']['last_connect'];
        }
        return null;
    }
}