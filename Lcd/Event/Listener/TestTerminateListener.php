<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dragon
 * Date: 14-12-1
 * Time: 下午11:04
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Event\Listener;

use Lcd\Event\Events;

/**
 * 终止事件监听测试
 * Class TestTerminateListener
 * @package Lcd\Event\Listener
 */
class TestTerminateListener extends Listener {
    public function implementedEvents(){
        return array(
            Events::TERMINATE=>'test'
        );
    }

    /**
     * 监听处理方法
     */
    public function test(){
        echo '终止事件监听成功。。。<br/>';
    }
}