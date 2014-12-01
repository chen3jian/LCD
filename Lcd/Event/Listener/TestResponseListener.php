<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dragon
 * Date: 14-12-1
 * Time: 下午11:03
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Event\Listener;

use Lcd\Event\Events;

/**
 * 响应事件监听测试
 * Class TestResponseListener
 * @package Lcd\Event\Listener
 */
class TestResponseListener extends Listener {
    public function implementedEvents(){
        return array(
            Events::RESPONSE=>'test'
        );
    }

    /**
     * 监听处理方法
     */
    public function test(){
        echo '响应事件监听成功。。。<br/>';
    }
}