<?php
// +----------------------------------------------------------------------
// |公共函数库
// +----------------------------------------------------------------------
// |Data:2014-11-14-下午2:21
// +----------------------------------------------------------------------
// |Author: 吕志雄 <870923001@qq.com>
// +----------------------------------------------------------------------
/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}

/**
 * 生成URL链接
 * @param string $url
 * @param string $vars
 * @return null|string
 */
function U($url='',$vars=''){
    // 解析URL
    $info   =  parse_url($url);

    //操作
    $path = explode('/',$info['path']);
    $action = array_pop($path);
    $action = $action?$action:\Lcd\Network\Request::$action;

    //控制器
    $controller = array_pop($path);
    $controller = $controller?$controller:\Lcd\Network\Request::$controller;

    //模块
    $module = array_pop($path);
    $module = $module?$module:\Lcd\Network\Request::$module;

    $subMap = \Lcd\Core\Config::read('SUB_MAP');//站点别名配置

    //确定子域名
    if(!empty($subMap)){
        $m = strtolower($module);
        if(in_array($m,$subMap)){
            $module = $sub = array_keys($subMap,$m)[0];//获取子域名
        } else {
            $sub = 'www';
        }
    } else {
        $sub = 'www';
    }

    $subConfigDomain = \Lcd\Routing\Routing::getConfigSubDomain();
    //子域名未配置
    if(!in_array($sub,$subConfigDomain)){
        $sub = 'www';
    }

    //主域名
    $module = strtolower($module);

    //模块别名
    $route_alias = \Lcd\Core\Config::read('SUB_MAP')[$module];

    //路由配置
    $_urlConfig = \Lcd\Core\Config::block('Routing',$module);
    if(empty($_urlConfig)){
        $_urlConfig = \Lcd\Core\Config::block('Routing',$route_alias);
    }
    if(empty($_urlConfig)){
        $_urlConfig = \Lcd\Core\Config::block('Routing','Default');
    }
    if(empty($_urlConfig)){
        return null;
    }

    //控制器与操作别名处理
    if(!empty($_urlConfig['CONTROLLER_ACTION_MAP'])){
        $_controller_action_map = $_urlConfig['CONTROLLER_ACTION_MAP'];
        if($controller=is_in_map($controller,$_controller_action_map)){
            if(!($action=is_in_map($action,$_controller_action_map,$controller))){
                return null;
            }
        } else {
            return null;
        }
    }

    if($sub=='www'){
        $url = $module.'/'.$controller.'/'.$action;
    } else {
        $url = $controller.'/'.$action;
    }

    // 解析参数
    if(is_string($vars)) {
        parse_str($vars,$vars);
    }elseif(!is_array($vars)){
        $vars = array();
    }
    if(isset($info['query'])) { // 解析地址里面参数 合并到vars
        parse_str($info['query'],$params);
        $vars = array_merge($params,$vars);
    }

    if(!empty($vars)) {
        $vars   =   http_build_query($vars);
        $url   .=   '?'.$vars;
    }

    $url = __APP__.'/'.$url;

    $url   =  (is_ssl()?'https://':'http://').$sub.'.'.DOMAIN_SUFFIX.$url;

    return $url;
}

/**
 * 获取控制器或操作的别名，若没有，返回false
 * @param $element 要获取别名的控制器或操作
 * @param $map 控制器操作别名数组
 * @param string $controller 控制器别名，默认为空，若为空，则只获取控制器别名，否则获取操作别名
 * @return int|string
 */
function is_in_map($element,$map,$controller=''){
    foreach($map as $key=>$val){
        if(empty($controller)){
            //判断是否存在控制器别名，若存在，返回别名
            if($val['alias']==$element){
                return $key;
            }
        } else {
            //判断是否存在操作别名，若存在，返回操作别名
            if($key==$controller){
                foreach($val['action'] as $akey=>$action){
                    if($action==$element){
                        return $akey;
                    }
                }
                break;
            }
        }
    }
    return false;
}
?>