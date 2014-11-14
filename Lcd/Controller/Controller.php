<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-18
 * Time: 下午3:22
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Controller;

use Lcd\Network\Response;
use Lcd\View\Engine\ViewVars;
use Lcd\View\View;
use Lcd\View\JsonView;
use Lcd\View\XmlView;

class Controller {

    /**
     * 模板输出变量
     * @var array
     * @access public
     */
    public $_viewVars = array();

    /**
     * 控制器可操作的方法
     * @var array
     * @access private
     */
    private $methods = array();

    /**
     * 架构方法
     * @access public
     */
    public function __construct() {
        //初使化模板变量
        $this->_viewVars = new ViewVars();

        //取出控制器可操作的方法
        $childMethods = get_class_methods($this);
        $parentMethods = get_class_methods('Lcd\Controller\Controller');
        $this->methods = array_diff($childMethods, $parentMethods);

        //控制器初始化
//        if(method_exists($this,'_initialize'))
//            $this->_initialize();
    }

    /**
     * 执行URL对应的操作
     * @access public
     * @param string $action 操作
     * @return string
     * @throws \Exception
     */
    public function invokeAction($action) {
//        echo $action;exit;
        try {
            $class = new \ReflectionClass($this);

            if($class->hasMethod($action)) {
                $method = $class->getMethod($action);
                //判断URL操作是有可用
                if (!$this->_isPrivateAction($method)) {
                    //控制器初始化
                    if($class->hasMethod('_initialize')) {
                        $initialize = $class->getMethod('_initialize');
                        if($initialize->isPublic()) {
                            $return = $initialize->invoke($this);
                        }
                    }
                    //执行URL对应的操作
                    if(empty($return)) {
                        $return = $method->invoke($this);
                    }
                    return $return;
                }
            }

            throw new \Exception($action . ' 不是有效的操作');

        } catch (\ReflectionException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 判断是否可用的URL操作
     * @access private
     */
    private function _isPrivateAction(\ReflectionMethod $method) {
        return $method->name[0] === '_' || !$method->isPublic() || !in_array($method->name, $this->methods);
    }

    /**
     * @access public
     * @param mixed $name
     * @param string $value
     * @return void
     */
    public final function assign($name, $value = null) {
        $this->_viewVars->assign($name,$value);
    }

    /**
     * @access public
     * @param mixed $name
     * @param string $value
     * @return void
     */
    public function __set($name,$value) {
        $this->_viewVars->assign($name,$value);
    }

    /**
     * 取得模板显示变量的值
     * @access protected
     * @param string $name 模板显示变量
     * @return mixed
     */
    public function get($name='') {
        return $this->_viewVars->get($name);
    }

    /**
     * 取得模板显示变量的值
     * @access protected
     * @param string $name 模板显示变量
     * @return mixed
     */
    public function __get($name) {
        return $this->_viewVars->get($name);
    }


    /**
     * 输出html
     * @access public
     * @param string $name 输出模板
     * @return string
     */
    public final function template($name = '') {
        $view = new View($this);
        echo $view->fetch($name);
    }

    /**
     * 输出json
     * @access public
     * @param array $data 要返回的JSON数据
     * @return string
     */
    public final function json($data = array()) {
        $view = new JsonView($this);
        return $view->fetch($data);
    }


    /**
     * 输出xml
     * @access public
     * @param string|array $data 要返回的XML数据或都是xml模板
     * @return string
     */
    public final function xml($data = null) {
        $view = new XmlView($this);
        return $view->fetch($data);
    }

    /**
     * 显示内容
     * @access public
     * @param string $content
     * @return string
     */
    public final function show($content) {
        echo $content;
    }
}