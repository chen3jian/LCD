<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-11-19
 * Time: 下午7:28
 */

namespace Lcd\Event;


final class Events {
    /**
     * 请求事件
     */
    const REQUEST = 'lcd.request';

    /**
     * 请示异常事件
     */
    const EXCEPTION = 'lcd.exception';

    /**
     * 模板事件
     */
    const VIEW = 'lcd.view';

    /**
     * 控制器事件
     */
    const CONTROLLER = 'lcd.controller';

    /**
     * 响应事件
     */
    const RESPONSE = 'lcd.response';

    /**
     * 终止事件
     */
    const TERMINATE = 'lcd.terminate';

    /**
     * 请求完成事件
     */
    const FINISH_REQUEST = 'lcd.finish_request';
} 