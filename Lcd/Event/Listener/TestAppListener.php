<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-11-21
 * Time: 上午11:25
 */

namespace Lcd\Event\Listener;


use Lcd\Event\Events;

/**
 * 应用开始事件监听测试
 * Class TestListener
 * @package Lcd\Event\Listener
 */
class TestAppListener extends  Listener {
    public function implementedEvents(){
        return array(
            Events::APP=>'test'
        );
    }

    /**
     * 监听处理方法
     */
    public function test(){
        echo '应用开始事件监听成功。。。<br/>';
    }
} 