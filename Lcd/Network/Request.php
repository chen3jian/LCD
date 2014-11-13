<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-2-21
 * Time: 上午12:20
 */

namespace Lcd\Network;

use Lcd\Routing\Routing;

class Request {

    //请求类型
    static $requestTime = null;
    static $isCli = null;
    static $isCgi = null;
    static $isWin = null;

    //访问类型
    static $requestMethod = null;
    static $isPost = null;
    static $isGet = null;
    static $isPut = null;
    static $isDelete = null;
    static $isAjax = null;

    static $url = null;

    //域名信息
    static $domain1 = null;
    static $subDomain = null;
    static $domain2 = null;
    static $domain3 = null;

    //站点信息
    static $siteDomain;
    static $urlInfo = array();

    //控制器信息
    static $module = null;
    static $controller = null;
    static $action = null;

    //模板主题
    static $theme = 'Default';

    /**
     * 初使化
     */
    public static function init() {
        self::$requestTime = $_SERVER['REQUEST_TIME'];
        self::$isCli = PHP_SAPI=='cli';
        self::$isCgi = strpos(PHP_SAPI,'cgi')===0;
        self::$isWin = strpos(PHP_OS,'WIN')!==false;

        self::$requestMethod = $_SERVER['REQUEST_METHOD'];
        self::$isPost = self::$requestMethod == 'POST';
        self::$isGet = self::$requestMethod == 'GET';
        self::$isPut = self::$requestMethod == 'PUT';
        self::$isDelete = self::$requestMethod == 'DELETE';
        self::$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        self::$url = Routing::getUrl();

        $domain = Routing::getDomain();
        self::$subDomain = $domain['subDomain'];
        self::$domain1 = $domain['domain1'];
        self::$domain2 = $domain['domain2'];
        self::$domain3 = $domain['domain3'];
        self::$siteDomain = $domain['siteDomain'];

        self::$module = Routing::getModule();
        self::$controller = Routing::getController();
        self::$action = Routing::getAction();

        //URL信息
        self::$urlInfo = Routing::getUrlInfo();
    }

    /**
     * 获取客户端IP
     * @return string
     */
    public static function clientIp() {
        return '';
    }

    /**
     * 获取城市等信息
     * @return array
     */
    public static function getCityInfo() {
        return array();
    }

    /**
     * 获取请求头信息
     * @param $name
     * @return bool|mixed|null|string
     */
    public static function header($name) {
        $name = 'HTTP_' . str_replace('-', '_', $name);
        return env($name);
    }


}