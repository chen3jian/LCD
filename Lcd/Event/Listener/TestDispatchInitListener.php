<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dragon
 * Date: 14-12-1
 * Time: 下午11:17
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Event\Listener;


use Lcd\Event\Events;

/**
 * 调度初始化事件监听测试
 * Class TestDispatchInitListener
 * @package Lcd\Event\Listener
 */
class TestDispatchInitListener extends Listener {
    public function implementedEvents(){
        return array(
            Events::DISPATCH_INIT=>'test'
        );
    }

    /**
     * 监听处理方法
     */
    public function test(){
        echo '调度初始化事件监听成功。。。<br/>';
    }
}