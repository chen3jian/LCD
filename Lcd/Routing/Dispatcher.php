<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-18
 * Time: 下午3:10
 * To change this template use File | Settings | File Templates.
 */
namespace Lcd\Routing;

use Lcd\Controller\Controller;
use Lcd\Network\Request;
use Lcd\Network\Response;

class Dispatcher {
    private static $module = null;
    private static $controller = null;
    private static $action = null;

    /**
     * 初使化并验证数据
     */
    public static function init() {
        self::$module = Request::$module;
        self::$controller = Request::$controller;
        self::$action = Request::$action;
    }

    /**
     * 执行调度
     * @return string
     * @throws \Exception
     */
    public static function dispatch() {
        $controller = self::getController();
        if(!($controller instanceof Controller)) {
            throw new \Exception('非法操作');
        }
        $content = $controller->invokeAction(self::$action);
        Response::body($content);
        Response::send();
    }

    /**
     * 获取控制器实例
     * @return object
     * @throws \Exception
     */
    public static function getController() {
        $controllerName = self::getControllerName();

        $reflection = new \ReflectionClass($controllerName);

        //如果类是抽象,或接口则返回错误
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            throw new \Exception("$controllerName 控制器错误");
        }

        //返回控制器的实例
        return $reflection->newInstance();
    }

    /**
     * 获取控制器名称
     * @return string
     * @throws \Exception
     */
    public static function getControllerName() {
        $controller = '\\'.self::$module.'\\'.'Controller'.'\\'.self::$controller;

        if(class_exists($controller)) {
            return $controller;
        }

        throw new \Exception("$controller 控制器不存在");
    }
}