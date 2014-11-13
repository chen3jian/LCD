<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-3-27
 * Time: 上午10:13
 */

namespace Lcd\View\Engine;

class ViewVars {

    /**
     * 模板输出变量
     * @var array
     * @access public
     */
    public $data = array();

    /**
     * 设置模板变量
     * @access public
     * @param string|array $name 模板变量的名称
     * @param string $value 模板变量的值
     * @return void
     */
    public final function assign($name, $value = '') {
        if(is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } else {
            $this->data[$name] = $value;
        }
    }

    /**
     * 获取模板变量
     * @access public
     * @param string $name 要获取的模板变量名称
     * @return mixed
     */
    public final function get($name = '') {
        if(empty($name)) {
            return $this->data;
        }
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

}