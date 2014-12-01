<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-11-21
 * Time: 上午10:38
 */

namespace Lcd\Event;

/**
 * 系统所有事件
 * Class Events
 * @package Lcd\Event
 */
final class Events {
    /**
     * 进入应用程序事件
     */
    const APP = 'lcd.entry';

    /**
     * 离开、终止应用程序
     */
    const TERMINATE = 'lcd.terminate';

    /**
     * 路由初始化事件
     */
    const ROUTE = 'lcd.route';

    /**
     * 请求初始化事件
     */
    const REQUEST = 'lcd.request';

    /**
     * 响应初始化事件
     */
    const RESPONSE = 'lcd.response';

    /**
     * 调度初始化事件
     */
    const DISPATCH_INIT = 'lcd.dispatch_init';

    /**
     * 调度事件
     */
    const DISPATCH = 'lcd.dispatch';
} 