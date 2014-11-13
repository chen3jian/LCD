<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-3-14
 * Time: 下午4:12
 */
return array(
    'TAGLIB_BEGIN'=>'<',
    'TAGLIB_END'=>'>',
    'TMPL_L_DELIM'=>'{',
    'TMPL_R_DELIM'=>'}',

    'TAGLIB_LOAD'=>true,
    'TAGLIB_PRE_LOAD'=>'',
    'TAGLIB_BUILD_IN'=>'Cx',
    'TMPL_DENY_PHP'=>false,
    'TMPL_DENY_FUNC_LIST'=>'echo,exit'
);
///* 模板引擎设置 */
//'TMPL_CONTENT_TYPE'     =>  'text/html', // 默认模板输出类型
//    'TMPL_ACTION_ERROR'     =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
//    'TMPL_ACTION_SUCCESS'   =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
//    'TMPL_EXCEPTION_FILE'   =>  THINK_PATH.'Tpl/think_exception.tpl',// 异常页面的模板文件
//    'TMPL_DETECT_THEME'     =>  false,       // 自动侦测模板主题
//    'TMPL_TEMPLATE_SUFFIX'  =>  '.html',     // 默认模板文件后缀
//    'TMPL_FILE_DEPR'        =>  '/', //模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符
//    // 布局设置
//    'TMPL_ENGINE_TYPE'      =>  'Think',     // 默认模板引擎 以下设置仅对使用Think模板引擎有效
//    'TMPL_CACHFILE_SUFFIX'  =>  '.php',      // 默认模板缓存后缀
//    'TMPL_DENY_FUNC_LIST'   =>  'echo,exit',    // 模板引擎禁用函数
//    'TMPL_DENY_PHP'         =>  false, // 默认模板引擎是否禁用PHP原生代码
//    'TMPL_L_DELIM'          =>  '{',            // 模板引擎普通标签开始标记
//    'TMPL_R_DELIM'          =>  '}',            // 模板引擎普通标签结束标记
//    'TMPL_VAR_IDENTIFY'     =>  'array',     // 模板变量识别。留空自动判断,参数为'obj'则表示对象
//    'TMPL_STRIP_SPACE'      =>  true,       // 是否去除模板文件里面的html空格与换行
//    'TMPL_CACHE_ON'         =>  true,        // 是否开启模板编译缓存,设为false则每次都会重新编译
//    'TMPL_CACHE_PREFIX'     =>  '',         // 模板缓存前缀标识，可以动态改变
//    'TMPL_CACHE_TIME'       =>  0,         // 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
//    'TMPL_LAYOUT_ITEM'      =>  '{__CONTENT__}', // 布局模板的内容替换标识
//    'LAYOUT_ON'             =>  false, // 是否启用布局
//    'LAYOUT_NAME'           =>  'layout', // 当前布局名称 默认为layout
//
//    // Think模板引擎标签库相关设定
//    'TAGLIB_BEGIN'          =>  '<',  // 标签库标签开始标记
//    'TAGLIB_END'            =>  '>',  // 标签库标签结束标记
//    'TAGLIB_LOAD'           =>  true, // 是否使用内置标签库之外的其它标签库，默认自动检测
//    'TAGLIB_BUILD_IN'       =>  'cx', // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔 注意解析顺序
//    'TAGLIB_PRE_LOAD'       =>  '',   // 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔