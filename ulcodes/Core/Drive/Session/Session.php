<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/11
 * Time: 16:17
 */

namespace ulcodes\Core\Drive\Session;

class Session implements SessionInterface
{
    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    /**
     * session读取
     *
     * @param null $key
     * @return mixed|null
     */
    public function get($key = null)
    {
        if (is_null($key)) {
            return $_SESSION;
        } elseif (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return null;
        }
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function expire($second)
    {
        // TODO: Implement expire() method.
    }

    public function getId()
    {
        return session_id();
    }

    public function setId($id)
    {
        session_id($id);
    }

    public function delete($key)
    {
        if ($key == '*') {
            session_unset();
            session_destroy();
        } else {
            unset($_SESSION[$key]);
        }
    }
}