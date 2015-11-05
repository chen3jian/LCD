<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-14
 * Time: 上午10:00
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Core;

use Lcd\Event\EventManager;
use Lcd\Event\Events;
use Lcd\Event\Listener\TestAppListener;
use Lcd\Event\Listener\TestDispatchInitListener;
use Lcd\Event\Listener\TestDispatchListener;
use Lcd\Event\Listener\TestRequestListener;
use Lcd\Event\Listener\TestResponseListener;
use Lcd\Event\Listener\TestRouteListener;
use Lcd\Event\Listener\TestTerminateListener;
use Lcd\Network\Request;
use Lcd\Network\Response;
use Lcd\Routing\Dispatcher;
use Lcd\Routing\Routing;
header('Content-Type:text/html; charset=utf-8');
define('CACHE_PATH', ROOT_PATH . 'Cache' . DS);
//项目定义
define('CONFIG_PATH', ROOT_PATH . 'App' . DS . 'Config' . DS);
define('MODULE_PATH', ROOT_PATH . 'App' . DS . 'Module' . DS);

//缓存目录定义
define('DATA_CACHE_PATH', CACHE_PATH . 'Data' . DS);
define('LOG_CACHE_PATH', CACHE_PATH . 'Log' . DS);
define('SYSTEM_CACHE_PATH', CACHE_PATH . 'System' . DS);
define('TPL_CACHE_PATH', CACHE_PATH . 'Template' . DS);
class App {

    //自动加载注册数据
    static $_autoLoad = array();

    //运行应用
    public static function run() {
        $start_mem = memory_get_usage();
        $start_time = microtime(true);

        //初使化自动加载
        spl_autoload_register('self::classLoader');

        //载入系统配置
        Config::block('System');

        //加载系统函数库
        require(LCD_PATH.'Common/function.php');

        //加载系统支持
        require(LCD_PATH.'Basics.php');

        //致命错误捕获
        register_shutdown_function('Lcd\Core\Error::fatalError');

        //自定义错误处理
        set_error_handler('Lcd\Core\Error::appError');

        //自定义异常处理
        set_exception_handler('Lcd\Core\Error::appException');

        //设置系统时区
        date_default_timezone_set(Config::read('DEFAULT_TIMEZONE'));

        //路由初使化
        Routing::init();

        //请求初使化
        Request::init();

        //响应初使化
        Response::init();

        //调度初使化
        Dispatcher::init();

        //加载模块配置及模块函数
        $module = Request::$module;//获取模块名
        Config::$module();//加载模块配置
        $moduleFunctionPath = MODULE_PATH.DS.ucfirst($module).DS.'Common'.DS.'function.php';
        if(is_file($moduleFunctionPath)) {
            include "$moduleFunctionPath";//加载模块函数
        }

        //调度开始
        Dispatcher::dispatch();

        $end_time = microtime(true);
        $end_mem = memory_get_usage();

        $str = number_format(($end_mem-$start_mem)/1024, 3) . 'kb';
        $str .= "\r\n<br />\r\n";
        $str .= number_format(($end_time-$start_time)*1000000) . 'ms';
        file_put_contents('test.html', $str);
    }

    /**
     * 加载文件
     * @param string $name
     * @param string $path
     * @return void
     */
    public static function load($name, $path) {
        self::$_autoLoad[$name] = trim($path, '/');
    }

    /**
     * 自动加载方法
     * @param string $className
     * @return boolean
     */
    public static function classLoader($className) {
        //类文件名
        $className = str_replace('\\', DS, $className) . '.php';//把\替换为系统文件分隔符，如果是Windows，则不变，若为Linux则替换为/
        $alias = substr($className,0,strpos($className,DS));//截取到第一个文件分隔符的子串

        //拼接文件路径
        if($alias == 'Lcd') {
            $path = ROOT_PATH . $className;
        } else if($alias=='') {
            $path = ROOT_PATH . 'App' . DS . 'Entity' . DS . $className;//实体类加载
        } else if($alias=='Entity'){
            $path = ROOT_PATH . 'App' . DS . $className;//实体类加载
        } else if($alias=='Doctrine'){
            $className = substr($className,strpos($className,DS)+1);
            $path = ROOT_PATH . 'Lcd' . DS .'Doctrine' .DS .'Lib' . DS .$className;//数据库相关类
        } elseif(isset(self::$_autoLoad[$alias])) {
            $path = ROOT_PATH . self::$_autoLoad[$alias] . DS . $className;

        } else {
            $path = MODULE_PATH . $className;
        }

        if(!is_file($path))
            return false;

        return require $path;
    }
}