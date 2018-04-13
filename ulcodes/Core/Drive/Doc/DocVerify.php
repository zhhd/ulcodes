<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 19:44
 */

namespace ulcodes\Core\Drive\Doc;


use app\verify\Verify;
use ulcodes\Core\Drive\Helper\Helper;

class DocVerify
{
    /**
     * [{"name":"name","types":["int"],"desc":""},...]
     *
     * @param array $parserValues
     * @return bool
     */
    public function verify(array $parserValues)
    {
        foreach ($parserValues as $parserValue) {
            $name  = $parserValue['name'];
            $desc  = !empty($parserValue['desc']) ? $parserValue['desc'] : $name;
            $types = $parserValue['types'];
            $value = Helper::input($name);
            $min   = isset($parserValue['min']) ? $parserValue['min'] : 0;
            $max   = isset($parserValue['max']) ? $parserValue['max'] : 0;

            if (in_array('null', $types) && $value == null) {
            } else {
                foreach ($types as $type) {
                    switch ($type) {
                        case 'length':
                            $params = [[$value, $min, $max]];
                            $error  = [
                                'name' => $name,
                                'type' => $type,
                                'desc' => $desc,
                                'min'  => $min,
                                'max'  => $max,
                            ];
                            break;
                        default:
                            $params = [$value];
                            $error  = [
                                'name' => $name,
                                'type' => $type,
                                'desc' => $desc,
                            ];
                            break;
                    }

                    $result = call_user_func_array([$this, "{$type}Verify"], $params);
                    if (!$result) {
                        Verify::error($error);
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function nullVerify($value)
    {
        return true;
    }


    private function stringVerify($value)
    {
        if (is_null($value)) {
            return false;
        }
        return true;
    }

    private function intVerify($value)
    {
        if (!is_numeric($value)) {
            return false;
        }
        return true;
    }

    private function boolVerify($value)
    {
        if (!is_bool($value) && $value !== 'false' && $value !== 'true') {
            return false;
        }
        return true;
    }

    private function lengthVerify($param)
    {
        $value  = $param[0];
        $min    = $param[1];
        $max    = $param[2];
        $length = mb_strlen($value, 'utf8');

        if ($length < $min || $length > $max) {
            return false;
        }
        return true;
    }

    private function emailVerify($value)
    {
        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $value)) {
            return false;
        }
        return true;
    }

    private function phoneVerify($value)
    {
        if (!preg_match("/^1[34578]\d{9}$/", $value)) {
            return false;
        }
        return true;
    }
}