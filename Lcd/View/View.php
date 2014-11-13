<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-2-22
 * Time: 下午1:23
 */

namespace Lcd\View;

use Lcd\Controller\Controller;
use Lcd\Network\Request;

class View {

    /**
     * 控制器对象
     * @var Controller
     * @access public
     */
    public $controller = null;

    /**
     * 模板输出变量
     * @var Controller
     * @access public
     */
    public $viewVars = array();

    /**
     * 架构方法
     * @access public
     */
    public function __construct(Controller $controller) {
        if(is_object($controller) && $controller instanceof Controller) {
            $this->controller = $controller;
            $this->viewVars = &$controller->_viewVars->data;
        }
    }

    /**
     * @access public
     * @param string $viewFile 模板文件
     * @param array $data 模板变量
     * @return string
     */
    public function fetch($viewFile, $data = array()) {

        //模板文件路径
        $viewFile = $this->_getTemplate($viewFile);

        if(!is_file($viewFile)) {
            //这里对视图的模板进行处理
            $tpl = new Template();
            $tpl->parseTemplate($viewFile);
        }

        //模板变量
        if(empty($data)) {
            $data = $this->viewVars;
        }

        extract($data);
        ob_start();

        include "$viewFile";

        unset($viewFile);
        return ob_get_clean();
    }

    /**
     * 自动定位模板文件路径
     * @access private
     * @param string $name 模板名称
     * @return string
     * @throws \Exception
     */
    protected final function _getTemplate($name = '') {
        $name = trim($name, '/');

        //模板名称
        if(empty($name)) {
            $name = Request::$action;
        }

        if(strpos($name, '/')!==false) {

            $arr = explode('/', $name);

            $action = array_pop($arr);
            $controller = array_pop($arr);
            $module = !empty($arr) ? array_pop($arr) : Request::$module;

            if(!empty($arr)) {
                throw new \Exception('模板参数错误');
            }
        } else {
            $action = $name;
            $controller = Request::$controller;
            $module = Request::$module;
        }

        return TPL_CACHE_PATH . $module . DS . Request::$theme . DS . $controller . DS . ucfirst($action) . '.php';
    }

}