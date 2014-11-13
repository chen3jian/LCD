<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-3-19
 * Time: 下午1:42
 */

namespace Lcd\Cache\Engine;

use Lcd\Cache\CacheEngine;
use Memcached;

class MemcachedEngine extends CacheEngine {

    /**
     * 缓存对象
     * @var Memcached
     */
    protected $_cache = null;

    /**
     * @var Memcached
     */
    protected $_Memcached = null;

    protected $_defaultConfig = array(
        'compress' => false,
        'duration' => 3600,
        'groups' => array(),
        'login' => null,
        'password' => null,
        'persistent' => false,
        'prefix' => 'Lcd_',
        'probability' => 100,
        'serialize' => 'php',
        'servers' => array(
            array('127.0.0.1', 11211, 33)
        ),
    );

    protected $_serializers = array(
        'igbinary' => Memcached::SERIALIZER_IGBINARY,
        'json' => Memcached::SERIALIZER_JSON,
        'php' => Memcached::SERIALIZER_PHP
    );

    public function init() {

        if (!class_exists('Memcached')) {
            throw new \Exception('Memcached 类不存在');
        }

        if (defined('Memcached::HAVE_MSGPACK') && Memcached::HAVE_MSGPACK) {
            $this->_serializers['msgpack'] = Memcached::SERIALIZER_MSGPACK;
        }

        if (isset($this->_cache)) {
            return true;
        }

        $this->_cache = new Memcached($this->_config['persistent'] ? (string)$this->_config['persistent'] : null);
        $this->_setOptions();

        //如果存在服务则返回
        if (count($this->_cache->getServerList())) {
            return true;
        }

        //添加服务
        if (!$this->_cache->addServers($this->_config['servers'])) {
            return false;
        }

        //验证
        if ($this->_config['login'] !== null && $this->_config['password'] !== null) {
            if (!method_exists($this->_cache, 'setSaslAuthData')) {
                throw new \Exception('Memcached 扩展没有建立与SASL支持');
            }
            $this->_cache->setSaslAuthData($this->_config['login'], $this->_config['password']);
        }
    }

    /**
     * 设置memcached实例
     * @throws Exception 当memcached扩展没有所需的序列化引擎建立
     */
    protected function _setOptions() {
        $this->_cache->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);

        $serializer = strtolower($this->_config['serialize']);
        if (!isset($this->_serializers[$serializer])) {
            throw new \Exception(
                sprintf('%s 不是一个有效的序列化引擎 Memcached', $serializer)
            );
        }

        if ($serializer !== 'php' && !constant('Memcached::HAVE_' . strtoupper($serializer))) {
            throw new \Exception(
                sprintf('memcached伸展不与%s编译支持', $serializer)
            );
        }

        $this->_cache->setOption(Memcached::OPT_SERIALIZER, $this->_serializers[$serializer]);

        //Check for Amazon ElastiCache instance
        if (defined('Memcached::OPT_CLIENT_MODE') && defined('Memcached::DYNAMIC_CLIENT_MODE')) {
            $this->_cache->setOption(Memcached::OPT_CLIENT_MODE, Memcached::DYNAMIC_CLIENT_MODE);
        }

        $this->_cache->setOption(Memcached::OPT_COMPRESSION, (bool)$this->_config['compress']);
    }

    /**
     * 获取键名
     * @param $name
     * @return mixed
     */
    protected function _key($name) {
        return $name;
    }

    /**
     * 写缓存
     * @param $name
     * @param $value
     * @param null $duration
     * @return mixed|void
     */
    public function set($name, $value, $duration = null) {
        //时间处理
        $duration = $duration!==null ? $duration : $this->_config['duration'];
        if ($duration > 30 * 86400) {
            $duration = 0;
        }

        return $this->_cache->set($this->_key($name), $value, $duration);
    }

    /**
     * 加
     * @param $name
     * @param int $offset
     * @return mixed|void
     */
    public function inc($name, $offset = 1) {
        $this->_cache->increment($this->_key($name), $offset);
    }

    /**
     * 减
     * @param $name
     * @param int $offset
     * @return mixed|void
     */
    public function dec($name, $offset = 1) {
        $this->_cache->decrement($this->_key($name), $offset);
    }

    /**
     * 获取
     * @param $name
     * @return mixed|void
     */
    public function get($name) {
        $this->_cache->get($this->_key($name));
    }

    /**
     * 验
     * @param $name
     * @return mixed|void
     */
    public function check($name) {
        //$this->_cache->delete($this->_key($name));
    }

    /**
     * 删
     * @param $name
     * @return mixed|void
     */
    public function rm($name) {
        $this->_cache->delete($this->_key($name));
    }

    /**
     * 删除全部
     * @param $check
     * @return mixed|void
     */
    public function rmAll($check) {
        $this->_cache->flush();
    }
}