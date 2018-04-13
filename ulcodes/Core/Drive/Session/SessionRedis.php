<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/11
 * Time: 16:27
 */

namespace ulcodes\Core\Drive\Session;


use ulcodes\Core\Drive\Db\Redis\Redis;

class SessionRedis implements SessionInterface
{
    /**
     * cookie key
     *
     * @var string
     */
    private $_cookie_key = 'ULCODESSESSION';
    /**
     * 过期时间
     *
     * @var int
     */
    private $_expire = 3600;
    /**
     * Redis对象
     *
     * @var Redis
     */
    private $_redis;


    public function __construct($redis)
    {
        $this->_redis = $redis;
    }


    /**
     * session读取
     *
     * @param null $key
     * @return mixed|null
     */
    public function get($key = null)
    {
        // key 不存在
        $id = $this->getId();
        if (!$this->_redis->exists($id)) {
            $data = null;
        } else {
            // 反序列化数据
            $data = unserialize($this->_redis->get($id));
            if ($key != null) {
                $data = isset($data[$key]) ? $data[$key] : null;
            }
        }
        return $data;
    }

    public function set($key, $value)
    {
        $id = $this->getId();
        if (!$this->_redis->exists($id)) {
            $data = [];
        } else {
            $data = unserialize($this->_redis->get($id));
        }
        $data[$key] = $value;
        $this->_redis->setex($id, $this->_expire, serialize($data));
    }

    public function expire($second)
    {
        $this->_expire = $second;
    }

    public function delete($key)
    {
        $id = $this->getId();
        if ($key == '*') {
            if ($this->_redis->exists($id)) {
                $this->_redis->delete($id);
            }
        } else {
            $data   = $this->get();
            $expire = $this->_redis->ttl($id);
            if (isset($data[$key])) {
                unset($data[$key]);
                $this->_redis->setex($id, $expire, serialize($data));
            }
        }
    }

    public function getId()
    {
        if (isset($_COOKIE[$this->_cookie_key])) {
            $id = $_COOKIE[$this->_cookie_key];
        } else {
            $id = hash('sha256', uniqid(mt_rand(), true));;
            $this->setId($id);
        }
        return $id;
    }

    public function setId($id)
    {
        setcookie($this->_cookie_key, $id, 0, '/');
    }
}