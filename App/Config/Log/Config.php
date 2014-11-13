<?php
/**
 * Created by PhpStorm.
 * User: fuzhuzheng
 * Date: 14-3-7
 * Time: 下午4:21
 */
return array(
    'LOG_TIME_FORMAT' => ' c ',//日志时间格式
    'LOG_FILE_SIZE' => 2097152,//日志大小
    'LOG_PATH' => CACHE_PATH . 'Log' . DS,//日志保存路径
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO,DEBUG,SQL',//日志保存级别
);