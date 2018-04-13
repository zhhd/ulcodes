<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/11
 * Time: 16:18
 */

namespace ulcodes\Core\Drive\Session;


interface SessionInterface
{
    /**
     * 获取session
     *
     * @param $key
     * @return mixed
     */
    public function get($key = null);

    /**
     * 设置session
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value);

    /**
     * 设置过期时间
     *
     * @param $second
     * @return mixed
     */
    public function expire($second);

    /**
     * 清理session
     * 如何等于*，清理全部
     *
     * @param $key
     * @return mixed
     */
    public function delete($key);

    /**
     * 获取session id
     *
     * @return mixed
     */
    public function getId();

    /**
     * 设置session id
     *
     * @param $id
     * @return mixed
     */
    public function setId($id);
}