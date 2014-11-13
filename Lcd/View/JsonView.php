<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-2-22
 * Time: 下午2:12
 */

namespace Lcd\View;

use Lcd\Controller\Controller;
use Lcd\Network\Response;

class JsonView extends View {

    /**
     * 架构方法
     * @access public
     */
    public function __construct(Controller $controller) {
        parent::__construct($controller);
        if(is_object($controller) && $controller instanceof Controller) {
            Response::type('json');
        }
    }

    /**
     * @access public
     * @param mixed $data 数据
     * @param mixed $return 数据
     * @return string
     */
    public final function fetch($data = false, $return = null) {

        //定义Json数据
        if(is_array($data)) {
            $this->viewVars['_serialize'] = $data;
        }

        //处理Json字符串
        $return = null;
        if (isset($this->viewVars['_serialize'])) {
            $return = $this->_serialize($this->viewVars['_serialize']);
        } elseif ($data !== false) {
            $return = parent::fetch($data);
        }

        //jsonp格式返回数据
        if (!empty($this->viewVars['_jsonp'])) {
            $jsonpParam = $this->viewVars['_jsonp'];
            if ($this->viewVars['_jsonp'] === true) {
                $jsonpParam = 'callback';
            }
            if (isset($_GET[$jsonpParam])) {
                $return = sprintf('%s(%s)', $_GET[$jsonpParam], $return);
                Response::type('js');
            }
        }

        return $return;
    }

    /**
     * @access private
     * @param string|array $serialize 需要转换成json格式数据的数组
     * @return string
     */
    public final function _serialize($serialize) {
        if (is_array($serialize)) {
            $data = array();
            foreach ($serialize as $alias => $key) {
                if (is_numeric($alias)) {
                    $alias = $key;
                }
                if (array_key_exists($key, $this->viewVars)) {
                    $data[$alias] = $this->viewVars[$key];
                }
            }
            $data = !empty($data) ? $data : null;
        } else {
            $data = isset($this->viewVars[$serialize]) ? $this->viewVars[$serialize] : null;
        }

        $jsonOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
        if (isset($this->viewVars['_jsonOptions'])) {
            if ($this->viewVars['_jsonOptions'] === false) {
                $jsonOptions = 0;
            } else {
                $jsonOptions = $this->viewVars['_jsonOptions'];
            }
        }

        return json_encode($data, $jsonOptions);
    }

}