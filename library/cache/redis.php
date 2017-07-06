<?php
// $Id: memory.php 1987 2009-01-08 18:03:35Z dualface $

/**
 * 定义 QCache_Redis 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: memory.php 1987 2009-01-08 18:03:35Z dualface $
 * @package cache
 */

/**
 * QCache_Redis 在当次请求中使用内存来缓存数据
 *
 * @author Sugar Inc. <xiao3vv@gmail.com>
 * @version $Id: memory.php 1987 2009-01-08 18:03:35Z dualface $
 * @package cache
 */
class QCache_Redis
{

    //当前类实例对象
    private static $_class = null;
    //redis实例对象
    private $_redis = null;
    //redis服务器地址
    private $_host = '127.0.0.1';
    //redis端口
    private $_port = '6379';

    /**
     * QCache_Redis constructor.
     * @param string $host
     * @param string $port
     */
    public function __construct($host='', $port='')
    {
        $this->_redis = new Redis;
        $this->_redis->connect( ($host ? $host : $this->_host), ($port ? $port : $this->_port));
    }

    /**
     * 自动调用redis方法
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if(isset($arguments[2]))
        {
            return $this->_redis->$name($arguments[0], $arguments[1], $arguments[2]);
        }
        if(isset($arguments[1]))
        {
            return $this->_redis->$name($arguments[0], $arguments[1]);
        }
        if(isset($arguments[0]))
        {
            return $this->_redis->$name($arguments[0]);
        }
    }

    /**
     * 存入redis
     * @param $method redis方法
     * @param $key
     * @param $value
     * @param int $expire 过期时间
     * @return bool
     */
    private function _set($method, $key, $value, $expire=0)
    {
        $set = $this->_redis->$method($key, json_encode($value));
        if($expire > 0)
        {
            $this->_redis->expire($key, $expire);
        }
        return $set;
    }

    /**
     * set
     * @param $key 键值
     * @param $value 值
     * @param int $expire 过期时间
     * @return bool
     */
    public function set($key, $value, $expire=0)
    {
        return $this->_set('set', $key, $value, $expire);
    }

    /**
     * 获取指定键的值
     * @param $key
     * @return bool|string|array
     */
    public function get($key)
    {
        $str = $this->_redis->get($key);
        return $str === false ? false : json_decode($str, true);
    }

    /**
     * 设置过期时间
     * @param $key 键值
     * @param $expire 时间
     * @return bool
     */
    public function expire($key, $expire)
    {
        if($expire > 0)
        {
            return $this->_redis->expireAt($key, time() + $expire);
            #return $this->_redis->expire($key, $expire);
        }
        return false;
    }
    /**
     * 删除
     * @param $key 键值
     */
    public function delete($key)
    {
        return $this->_redis->delete($key);
    }

    public function remove($key)
    {
        return $this->delete($key);
    }

    /**
     * rpush
     * @param $key 键值
     * @param $value 值
     * @return int
     */
    public function push($key, $value, $expire = 0)
    {
        $ret = $this->_redis->lPush($key, $value);
        if($expire > 0)
        {
            $this->_redis->expire($key, $expire);
        }
        return $ret;
    }

    public function size($key)
    {
        $int = $this->_redis->lSize($key);
        return $int;
    }

    public function rpop($key)
    {
        $str = $this->_redis->rPop($key);
        return $str === false ? false : $str;
    }

    /**
     * 返回redis实例对象
     * @return null|Redis
     */
    public function redis()
    {
        return $this->_redis;
    }
    /**
     * 返回此类对象
     * @param string $host
     * @param string $port
     * @return null|QCache_Redis
     */
    public static function init($host='', $port='')
    {
        if(self::$_class === null)
        {
            //echo 'redis null', chr(10);
            self::$_class = new self($host, $port);
        }
        return self::$_class;
    }
}

