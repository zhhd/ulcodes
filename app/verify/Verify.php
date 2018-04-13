<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/11
 * Time: 10:06
 */

namespace app\verify;


use ulcodes\Core\Drive\Helper\Helper;

class Verify
{
    public static function error($param)
    {
        $type = $param['type'];
        $desc = $param['desc'];
        switch ($type) {
            case 'int':
                echo Helper::json(['state' => false, 'msg' => "{$desc}必须为数字"]);
                break;
            case 'string':
                echo Helper::json(['state' => false, 'msg' => "{$desc}不能为空"]);
                break;
            case 'bool':
                echo Helper::json(['state' => false, 'msg' => "{$desc}必须为真假类型"]);
                break;
            case 'length':
                $min = $param['min'];
                $max = $param['max'];
                echo Helper::json(['state' => false, 'msg' => "{$desc}长度必须在{$min}~{$max}之间"]);
                break;
            default:
                echo Helper::json(['state' => false, 'msg' => "{$desc}必须为{$type}类型"]);
                break;
                break;
        }
    }
}