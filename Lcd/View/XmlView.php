<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-2-22
 * Time: 下午2:11
 */

namespace Lcd\View;

use Lcd\Controller\Controller;
use Lcd\Network\Response;

class XmlView extends View {

    /**
     * 架构方法
     * @access public
     */
    public function __construct(Controller $controller) {
        parent::__construct($controller);
        if(is_object($controller) && $controller instanceof Controller) {
            Response::type('Xml');
        }
    }

    /**
     * @access public
     * @param mixed $data 数据
     * @return string
     */
    public final function fetch($data = false) {

        //定义Json数据
        if(is_array($data)) {
            $this->viewVars['_serialize'] = $data;
        }

        //处理Json字符串
        if (isset($this->viewVars['_serialize'])) {
            return $this->_serialize($this->viewVars['_serialize']);
        }
        return parent::fetch($data);
    }

    /**
     * XML
     * @access private
     * @param array $serialize 需要转换成xml格式数据的数组
     * @return string
     */
    private final function _serialize($serialize) {
        //我们不用XML
        return implode(',', $serialize);
    }


}