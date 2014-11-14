<?php
/**
 * Created by PhpStorm.
 * User: fuzhuzheng
 * Date: 14-3-6
 * Time: 上午11:51
 */
return array(
    'DEFAULT_CHARSET'=>'utf-8',//默认输出字符集
    'APP_DEBUG'=>true,//应用调试
    'DEFAULT_TIMEZONE'=>'PRC',//默认
    'SUB_DOMAIN'=>array('www', 'blog'),//子站名
    'LOG_RECORD' => true,//是否写日志
    'LOG_TYPE'=>'',
    'LOG_PATH'=>'',
    'ERROR_PAGE' => '',//自定义错误页面,否则显示导演页面模板
    'TMPL_EXCEPTION_FILE' => '',//异常页面模板
    'SHOW_ERROR_MSG' => true,//是否显示系统错误信息,否则显示ERROR_MESSAGE(错误提示内容)
    'ERROR_MESSAGE' => ''//错误提示内容
);