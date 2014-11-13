<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-2-19
 * Time: 下午8:29
 */

namespace Lcd\Cache\Engine;

use Lcd\Cache\CacheEngine;
use Lcd\Core\Config;

class RedisEngine extends CacheEngine {

    /**
     * Redis连接
     * @var Redis
     * @access public
     */
    public $_cache = null;

    /**
     * 连接配置
     * @var array
     * @access private
     */
    protected $_config = array(
        'database' => 0,
        'duration' => 3600,
        'password' => false,
        'persistent' => true,
        'prefix' => null,
        'probability' => 100,
        'server' => '127.0.0.1',
        'port' => 6379,
        'timeout' => 0
    );

    //实例化,创建连接
    public function init() {
        //初使化配置
        $config = Config::block('Redis');
        $this->_config = $config + $this->_config;

        //处理连接
        try {
            //获取实例
            $this->_cache = new Redis();

            //长连接
            if (empty($this->_config['persistent'])) {
                $return = $this->_cache->connect($this->_config['server'], $this->_config['port'], $this->_config['timeout']);
            } else {
                $persistentId = $this->_config['port'] . $this->_config['timeout'] . $this->_config['database'];
                $return = $this->_cache->pconnect($this->_config['server'], $this->_config['port'], $this->_config['timeout'], $persistentId);
            }

            //密码验证
            if ($return && $this->_config['password']) {
                $return = $this->_cache->auth($this->_config['password']);
            }

            //连接Redis
            if(!$return) {
                throw new \Exception('Redis 服务不可用');
            }
        } catch ( \Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $return;
    }

    /**
     * 获取键名
     * @param string $name
     * @return string
     */
    public function _key($name) {
        //设置缓存为DB1
        $this->_cache->select($this->_config['database']);
        return $this->_config['prefix'] . $name;
    }

    /**
     * 写缓存
     * @param string $name
     * @param $value
     * @param int $duration
     * @return bool|mixed
     */
    public function set($name, $value, $duration = 0) {
        if (!is_int($value)) {
            $value = serialize($value);
        }

        //时间
        $duration = $duration === 0 ? $this->_config['duration'] : $duration;

        if ($duration === 0) {
            return $this->_cache->set($this->_key($name), $value);
        }

        return $this->_cache->setex($this->_key($name), $duration, $value);
    }

    /**
     * 加
     * @param string $name
     * @param int $offset
     * @return int|mixed
     */
    public function inc($name, $offset = 1) {
        return $this->_cache->incrBy($this->_key($name), $offset);
    }

    /**
     * 减
     * @param string $name
     * @param int $offset
     * @return int|mixed
     */
    public function dec($name, $offset = 1) {
        return $this->_cache->decrBy($this->_key($name), $offset);
    }

    /**
     * 读取缓存
     * @param string $name
     * @return bool|mixed|string
     */
    public function get($name) {
        $content = $this->_cache->get($this->_key($name));
        return strpos($content, '{')===0 ? unserialize($content) : $content;
    }

    /**
     * 验证是否存在
     * @param string $name
     * @return bool|mixed
     */
    public function check($name) {
        return $this->_cache->exists($this->_key($name));
    }

    /**
     * 删除缓存
     * @param string $name
     * @return int|mixed
     */
    public function rm($name) {
        return $this->_cache->del($this->_key($name));
    }

    //删除全部
    public function rmAll($check) {
    }
}

/**
 * Class Redis
 * @package Lcd\Cache\Engine
 */
class Redis extends \Redis {
    /**
     * 当前查询的DB
     * @var int
     */
    private $dbNum = 0;

    /**
     * @param int $dbNum
     * @return Redis
     */
    public function select($dbNum) {
        if($dbNum !== $this->$dbNum) {
            parent::select($dbNum);
        }
        return $this;
    }
}