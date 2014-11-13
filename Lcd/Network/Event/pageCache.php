<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-3-25
 * Time: 下午4:23
 */

namespace Lcd\Network\Response;

use Lcd\Cache\Cache;
use Lcd\Network\Request;

/**
 * Class pageCache
 * 给来缓存响应信息
 * @package Lcd\Network\Response
 */
class pageCache {

    /**
     * 设置缓存
     * @access public
     * @param string $page
     * @param array $key
     * @return mixed
     */
    public static final function set($page = '', $key = array()) {
        return Cache::Oss()->set(self::_parseKey($key), $page);
    }

    /**
     * 获取缓存
     * @access public
     * @param array $key
     * @return mixed
     */
    public static final function get($key = array()) {
        return Cache::Oss()->get(self::_parseKey($key));
    }

    /**
     * 删除缓存
     * @access public
     * @param array $key
     * @return mixed
     */
    public static final function rm($key = array()) {
        return Cache::Oss()->rm(self::_parseKey($key));
    }

    /**
     * 处理KEY
     * @access private
     * @param array $key
     * @return string
     * @throws \Exception
     */
    private static final function _parseKey($key = array()) {
        $_key = 'pageCache/';
        if(empty($key)) {
            $_key .= Request::$module . '/';
            $_key .= Request::$controller . '/';
            $_key .= Request::$action . '/';
            $key = Request::$urlInfo;
        } else {
            if(empty($key['module']) || empty($key['controller']) || empty($key['action'])) {
                throw new \Exception('参数不正确');
            }
            $_key .= $key['module'] . '/';
            $_key .= $key['controller'] . '/';
            $_key .= $key['action'] . '/';
            unset($key['module'],$key['controller'],$key['action']);
        }

        if(empty($key)) {
            throw new \Exception('参数不正确');
        }

        //排序
        ksort($key);

        //组装KEY
        $_key .= md5(serialize($key)) . '.txt';

        return $_key;
    }

    /**
     * 处理page信息
     */
    private static final function _parsePage($page = '') {
        //通过Response拼接缓存页面
        if(empty($page)) {
            $data = array();
            $data['header'] = array();
            $data['body'] = '';
        }
        return $page;
    }
}