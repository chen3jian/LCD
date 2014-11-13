<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-2-19
 * Time: 下午8:31
 */

namespace Lcd\Cache\Engine;

use Lcd\Cache\CacheEngine;

class XcacheEngine extends CacheEngine {

    /**
     * 实例化,创建连接
     */
    public function init() {
        if(!function_exists('xcache_isset'))
            throw new \Exception('Xcache 不可用');
    }

    /**
     * 获取键名
     * @param $name
     * @return mixed
     */
    public function _key($name) {
        return $name;
    }

    /**
     * 写
     * @param $name
     * @param $value
     * @param null $expire
     * @return bool|mixed
     */
    public function set($name, $value, $expire = null) {
        if(is_null($expire))
            $expire = 10;
        return xcache_set($this->_key($name), $value, $expire);
    }

    /**
     * 加1
     * @param $name
     * @param int $offset
     * @return int|mixed
     */
    public function inc($name, $offset = 1) {
        return xcache_inc($this->_key($name), $offset);
    }

    /**
     * 减1
     * @param $name
     * @param int $offset
     * @return int|mixed
     */
    public function dec($name, $offset = 1) {
        return xcache_dec($this->_key($name), $offset);
    }

    /**
     * 读
     * @param $name
     * @return mixed
     */
    public function get($name) {
        return xcache_get($this->_key($name));
    }

    /**
     * 验
     * @param $name
     * @return bool|mixed
     */
    public function check($name) {
        return xcache_isset($this->_key($name));
    }

    /**
     * 删
     * @param $name
     * @return bool|mixed
     */
    public function rm($name) {
        return xcache_unset($this->_key($name));
    }

    /**
     * 删除全部
     * @param $check
     * @return bool|mixed
     */
    public function rmAll($check) {
        $max = xcache_count(XC_TYPE_VAR);
        for ($i = 0; $i < $max; $i++) {
            xcache_clear_cache(XC_TYPE_VAR, $i);
        }
        return true;
    }
}