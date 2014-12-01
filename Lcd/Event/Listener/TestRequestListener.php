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
 * 请求事件监听成功
 * Class TestRequestListener
 * @package Lcd\Event\Listener
 */
class TestRequestListener extends Listener {
    public function implementedEvents(){
        return array(
            Events::REQUEST=>'test'
        );
    }

    /**
     * 监听处理方法
     */
    public function test(){
        echo '请求事件监听成功。。。<br/>';
    }
}