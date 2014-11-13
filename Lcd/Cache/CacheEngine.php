<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-19
 * Time: 下午5:58
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Cache;

abstract class CacheEngine {
    /**
     * @var array
     */
    protected $_config = array();

    /**
     * 连接对象
     * @var null
     */
    public $_cache = null;

    /**
     * 架构方法
     */
    public function __construct() {
        if(method_exists($this, 'init')) {
            $this->init();
        }
    }

    /**
     * 获取键名
     * @param $name
     * @return mixed
     */
    abstract protected function _key($name);

    /**
     * 设置缓存
     * @param $name
     * @param $value
     * @param null $expire
     * @return mixed
     */
    abstract public function set($name, $value, $expire = null);

    /**
     * 设置加1
     * @param $name
     * @param int $offset
     * @return mixed
     */
    abstract public function inc($name, $offset = 1);

    /**
     * 设置减
     * @param $name
     * @param int $offset
     * @return mixed
     */
    abstract public function dec($name, $offset = 1);

    /**
     * 获取缓存
     * @param $name
     * @return mixed
     */
    abstract public function get($name);

    /**
     * 验证缓存是否有效
     * @param $name
     * @return mixed
     */
    abstract public function check($name);

    /**
     * 删除缓存
     * @param $name
     * @return mixed
     */
    abstract public function rm($name);

    /**
     * 删除全部缓存
     * @param $check
     * @return mixed
     */
    abstract public function rmAll($check);

}