<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-2-19
 * Time: 下午8:31
 */

namespace Lcd\Cache\Engine;

use Lcd\Cache\CacheEngine;
use Lcd\Utility\Oss;

class OssEngine extends CacheEngine {

    /**
     * OSS资源
     * @var Oss
     */
    private $_cache = null;

    /**
     * @var null
     */
    private $bucket = null;

    /**
     * @var null
     */
    private $prefix = null;

    //实例化,创建连接
    public function init() {
        $this->_cache = Oss::factory();
        $this->bucket = Oss::$config['CacheBucket'];
        $this->prefix = Oss::$config['CacheKeyPrefix'];
    }

    //获取键名
    public function _key($name) {
        return $this->prefix . $name;
    }

    //写
    public function set($name, $value, $expire = null) {
        $result = $this->_cache->putObject(array(
            'Bucket' => $this->bucket,
            'Key' => $this->_key($name),
            'Content' => $value,
        ));
        return $result->getETag();
    }

    //加1
    public function inc($name, $offset = 1) {
        throw new \Exception('操作错误');
    }

    //减1
    public function dec($name, $offset = 1) {
        throw new \Exception('操作错误');
    }

    //读取
    public function get($name) {
        return $this->_cache->getObject(array(
            'Bucket' => $this->bucket,
            'Key' => $this->_key($name)
        ));
    }

    //验证设置
    public function check($name) {
        throw new \Exception('操作错误');
    }

    //删除
    public function rm($name) {
        $this->_cache->deleteObject(array(
            'Bucket' => $this->bucket,
            'Key' => $this->_key($name)
        ));
    }

    //删除全部
    public function rmAll($check) {
        throw new \Exception('操作错误');
    }
}