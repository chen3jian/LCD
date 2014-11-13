<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-14
 * Time: 上午10:00
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Event;

interface EventListener {

    /**
     * 事件监听注册表
     * @return array
     */
    public function implementedEvents();

}