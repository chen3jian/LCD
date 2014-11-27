<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-11-21
 * Time: 上午11:25
 */

namespace Lcd\Event\Listener;


use Lcd\Event\EventListener;
use Lcd\Event\Events;

/**
 * 测试监听者
 * Class TestListener
 * @package Lcd\Event\Listener
 */
class TestListener implements EventListener {
    public function implementedEvents(){
        return array(
            Events::APP=>'test'
        );
    }

    /**
     * 监听处理方法
     */
    public function test(){
        echo '事件监听成功。。。';
    }
} 