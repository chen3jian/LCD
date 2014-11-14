<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-19
 * Time: 上午10:02
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Routing;

use Lcd\Core\Config;

class Routing {

    /**
     * 域名的解析数据
     * @var array
     * @access private
     */
    private static $_domain = array();

    /**
     * 系统配置支持的子域名
     * @var array
     */
    private static $_configSubDomain = array();

    /**
     * $_SESSION['PATH_INFO']的解析数据
     * @var array
     * @access private
     */
    private static $_pathInfo = array();

    /**
     * 站点配置
     * @var array
     * @access private
     */
    private static $_siteConfig = array();

    /**
     * 站点的路由配置
     * @var array
     * @access private
     */
    private static $_urlInfo = array();

    /**
     * 初使化用户的请求
     * 解析域名
     * 初使化站点
     * 解析pathInfo
     * @access public
     * @return void
     * @throws \Exception
     */
    public static function init() {
//        self::$_configSubDomain = Config::System('sub_domain');
        self::$_configSubDomain = Config::read('SUB_DOMAIN');

        if(empty(self::$_configSubDomain)){
            self::$_configSubDomain[] = 'www';//获取没有配置则直接取www
        }

        self::_domainInit();//初始化$_domain

        if(!in_array(self::$_domain['currentSubDomain'],self::$_configSubDomain)){
            throw new \Exception('站点配置错误，本系统不支持子域名：'.self::$_domain['domain']);
        }

        self::_pathInfoInit();//初始化$_pathInfo

        if(empty(self::$_pathInfo) || empty(self::$_pathInfo[0])){
            //没有pathInfo信息
            $route_config = self::$_domain['currentSubDomain'];//指定路由配置文件名
        } else {
            //有pathInfo信息
            if(self::$_domain['currentSubDomain']=='www'){
                //如果有子域名，就不能通过其他站点访问
                if(in_array(self::$_pathInfo[0],self::$_configSubDomain)){
                    throw new \Exception('模块错误，请使用子域名访问本模块：'.$module.'.'.DOMAIN_SUFFIX);
                }
                $route_config = self::$_pathInfo[0];
            } else {
                $route_config = self::$_domain['currentSubDomain'];//指定路由配置文件名
            }
        }

        //初使化站点配置
        //获取路由相关配置信息
        if($_urlConfig = Config::block('Routing',$route_config)){
            self::$_domain['siteDomain'] = self::$_domain['currentSubDomain'];
        } else if($_urlConfig = Config::block('Routing','Default')) {
            self::$_domain['siteDomain'] = self::$_domain['currentSubDomain'];
        } else {
            throw new \Exception('站点初使化错误');
        }

        if(empty($_urlConfig)){
            throw new \Exception('URL错误');
        }

//        if($_config = Config::block('Routing',self::$_domain['currentSubDomain'])) {
//            self::$_domain['siteDomain'] = self::$_domain['currentSubDomain'];
////        } elseif($_config = Config::block('Routing',self::$_domain['domain2'])) {
////            self::$_domain['siteDomain'] = self::$_domain['domain2'];
//        } elseif($_config = Config::block('Routing','Default')) {
//            self::$_domain['siteDomain'] = 'Default';
//        } else {
//            throw new \Exception('站点初使化错误');
//        }

        if(isset($_urlConfig['CACHE_PAGE'])) {
            self::$_urlInfo['cachePage'] = $_urlConfig['CACHE_PAGE'];
            unset($_urlConfig['CACHE_PAGE']);
        }

        //解析URL信息
        foreach($_urlConfig as $key => $config) {
            self::$_urlInfo[$key] = self::parseParam($config);
        }

        //站点配置
        self::$_siteConfig = $_urlConfig;

        return;

        //URL配置别名
//        $urlConfigAlias = self::parseParam($_config['URL_CONFIG_ALIAS']);
//        if($_urlConfig = $_config['URL_CONFIG'][$urlConfigAlias]) {
//            //销毁初使化配置
//            unset($_config['URL_CONFIG_ALIAS'],$_config['URL_CONFIG']);
//
//            if(isset($_urlConfig['CACHE_PAGE'])) {
//                self::$_urlInfo['cachePage'] = $_urlConfig['CACHE_PAGE'];
//                unset($_urlConfig['CACHE_PAGE']);
//            }
//
//            //解析URL信息
//            foreach($_urlConfig as $key => $config) {
//                self::$_urlInfo[$key] = self::parseParam($config);
//            }
////            var_dump(self::$_urlInfo);exit;
//
//            //站点配置
//            self::$_siteConfig = $_config;
//
//            return;
//        }

//        throw new \Exception('URL错误');
    }

    /**
     * 域名初使化
     * @return void
     */
    public static function _domainInit() {
//        self:$subDomain = Config::System('sub_domain');
        $_SERVER['HTTP_HOST'] = strtolower($_SERVER['HTTP_HOST']);//如：http://www.topjz.com/
        self::$_domain['domain'] = $_SERVER['HTTP_HOST'];//域名
//        self::$_domain['domain1'] = Config::read('DOMAIN');//如：.topjz.com/
//        self::$_domain['domain1'] = '.'.DOMAIN_SUFFIX;//如：.topjz.com/
//        self::$_domain['subDomain'] = substr($_SERVER['HTTP_HOST'], 0 ,strpos($_SERVER['HTTP_HOST'], self::$_domain['domain1']));//子域名，如www,m,api,g,img等等
        self::$_domain['currentSubDomain'] = substr($_SERVER['HTTP_HOST'], 0 ,strpos($_SERVER['HTTP_HOST'], '.'));//当前子域名，如www,m,api,g,img等等
//        $_domain = explode('.', self::$_domain['subDomain']);
//        var_dump($_domain);exit;
//        $_domain = array(self::$_domain['subDomain']);
//        self::$_domain['domain2'] = array_pop($_domain);
//        if(!empty($_domain)) {
//            self::$_domain['domain3'] = array_pop($_domain);
//        } else {
//            self::$_domain['domain3'] = null;
//        }
    }

    /**
     * 解析PathInfo成数组
     * @access public
     * @return void
     */
    private static function _pathInfoInit() {
        //获取PATH_INFO
        if(isset($_GET['path_info_str']) && !empty($_GET['path_info_str'])) {
            $_pathInfo = $_GET['path_info_str'];
        } elseif(isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
            $_pathInfo = $_SERVER['PATH_INFO'];
        } else {
            $_pathInfo = '';
        }

        $_SERVER['PATH_INFO'] = strtolower($_pathInfo);
        //PATH_INFO
        self::$_pathInfo = explode('/', trim($_pathInfo, '/'));
    }

    public static function getUrl() {
        return '';
    }

    /**
     * 返回解析后的域名信息
     * @access public
     * @return array
     */
    public static function getDomain() {
        return self::$_domain;
    }

    public static function getUrlInfo() {
        return self::$_urlInfo;
    }

    /**
     * 返回解析后的域名信息
     * @access public
     * @return string
     * @throws \Exception
     */
    public static function getModule() {
        if(isset(self::$_urlInfo['MODULE']) && ($module = self::$_urlInfo['MODULE'])) {
            unset(self::$_urlInfo['MODULE']);
            return self::_ucFirst($module);
        }
        throw new \Exception('MODULE 错误');
    }

    /**
     * 返回解析后的域名信息
     * @access public
     * @return string
     * @throws \Exception
     */
    public static function getController() {
        if(isset(self::$_urlInfo['CONTROLLER']) && ($controller = self::$_urlInfo['CONTROLLER'])) {
            unset(self::$_urlInfo['CONTROLLER']);
            return self::_ucFirst($controller);
        }
        throw new \Exception('CONTROLLER 错误');
    }

    /**
     * 返回解析后的域名信息
     * @access public
     * @return string
     * @throws \Exception
     */
    public static function getAction() {
        if(isset(self::$_urlInfo['ACTION']) && ($action = self::$_urlInfo['ACTION'])) {
            unset(self::$_urlInfo['ACTION']);
            return self::_ucFirst($action, false);
        }
        throw new \Exception('ACTION 错误');
    }

    /**
     * 将下横线后的字母转为大写(URL用)
     * @param string $name 要转换的字符串
     * @param bool $first 是否第一个字符串转为大写
     * @return string
     * @throws \Exception
     */
    public static function _ucFirst($name, $first = true) {
        //第一个字符串不能为下横线
        if(strpos($name, '_') === 0) {
            throw new \Exception("$name 的第一个字符不给为'_'");
        }

        if($first) {
            return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
        } else {
            return preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name);
        }
    }

    /**
     * 通过配置文件处理参数
     * @access public
     * @param array $config 通过配置获取解析后的参数
     * @return string
     */
    public static function parseParam(array $config) {
        $config[1] = isset($config[1]) ? $config[1] : '';

        switch($config[1]) {
            case 'domain':
                $param = isset(self::$_domain[$config[2]]) && !empty(self::$_domain[$config[2]]) ? self::$_domain[$config[2]] : $config[0];
                break;
            case 'pathInfo':
                $param = isset(self::$_pathInfo[$config[2]]) && !empty(self::$_pathInfo[$config[2]]) ? self::$_pathInfo[$config[2]] : $config[0];
                break;
            case 'get':
                $param = isset($_GET[$config[2]]) && !empty($_GET[$config[2]]) ? $_GET[$config[2]] : $config[0];
                break;
            case 'post':
                $param = isset($_POST[$config[2]]) && !empty($_POST[$config[2]]) ? $_POST[$config[2]] : $config[0];
                break;
            default:
                $param = $config[0];
        }

        return $param;
    }
}