<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dragon
 * Date: 14-12-1
 * Time: 下午10:58
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Event\Listener;


use Lcd\Event\Events;

/**
 * 路由事件监听测试
 * Class TestRouteListener
 * @package Lcd\Event\Listener
 */
class TestRouteListener extends Listener {
    public function implementedEvents(){
        return array(
            Events::ROUTE=>'test'
        );
    }

    /**
     * 监听处理方法
     */
    public function test(){
        echo '路由事件监听成功。。。<br/>';
    }
}