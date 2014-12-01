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
 * 调度事件监听测试
 * Class TestDispatchListener
 * @package Lcd\Event\Listener
 */
class TestDispatchListener extends Listener {
    public function implementedEvents(){
        return array(
            Events::DISPATCH=>'test'
        );
    }

    /**
     * 监听处理方法
     */
    public function test(){
        echo '调度事件监听成功。。。<br/>';
    }
}