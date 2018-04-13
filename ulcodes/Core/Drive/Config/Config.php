<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 15:28
 */

namespace ulcodes\Core\Drive\Config;

class Config
{
    /**
     * 开发环境下配置信息
     *
     * @var
     */
    private static $config_debug;

    /**
     * @return object
     */
    public static function getXmlConfigDebug()
    {
        $baseDir = BASE_DIR;
        $dir     = DIRECTORY_SEPARATOR;
        if (!self::$config_debug) {
            self::$config_debug = simplexml_load_file("{$baseDir}{$dir}app{$dir}config{$dir}config_debug.xml");
            self::$config_debug = json_decode(json_encode(self::$config_debug), true);
        }
        return self::$config_debug;
    }

    /**
     * @return mixed
     */
    public static function getJsonConfigDebug()
    {
        $baseDir = BASE_DIR;
        $dir     = DIRECTORY_SEPARATOR;
        if (!self::$config_debug) {
            self::$config_debug = json_decode(file_get_contents("{$baseDir}{$dir}app{$dir}config{$dir}config_debug.json"), true);
        }
        return self::$config_debug;
    }

    /**
     * 生产环境下配置信息
     *
     * @var
     */
    private static $config;

    /**
     * @return object
     */
    public static function getXmlConfig()
    {
        $baseDir = BASE_DIR;
        $dir     = DIRECTORY_SEPARATOR;
        if (!self::$config) {
            self::$config = simplexml_load_file("{$baseDir}{$dir}app{$dir}config{$dir}config.xml");
            self::$config = json_decode(json_encode(self::$config), true);
        }
        return self::$config;
    }

    public static function getJsonConfig()
    {
        $baseDir = BASE_DIR;
        $dir     = DIRECTORY_SEPARATOR;
        if (!self::$config) {
            self::$config = json_decode(file_get_contents("{$baseDir}{$dir}app{$dir}config{$dir}config.json"), true);
        }
        return self::$config;
    }

    /**
     * 获取配置信息
     * @param        $name
     * @param string $ext
     * @return bool|int|null
     */
    public static function get($name, $ext = '.json')
    {
        if (DEBUG) {
            switch ($ext) {
                case '.json':
                    $config = self::getJsonConfigDebug();
                    break;
                case '.xml':
                    $config = self::getXmlConfigDebug();
                    break;
            }
        } else {
            switch ($ext) {
                case '.json':
                    $config = self::getJsonConfig();
                    break;
                case '.xml':
                    $config = self::getXmlConfig();
                    break;
            }
        }
        $value = isset($config[$name]) ? $config[$name] : null;
        if (is_numeric($value)) {
            $value = intval($value);
        } elseif ($value === 'false') {
            $value = false;
        } elseif ($value === 'true') {
            $value = true;
        }
        return $value;
    }
}