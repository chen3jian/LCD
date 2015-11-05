<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-18
 * Time: 下午5:51
 * To change this template use File | Settings | File Templates.
 */
define('DOMAIN_SUFFIX',substr(strstr($_SERVER['HTTP_HOST'],'.'),1));

//系统常量定义
const DS = DIRECTORY_SEPARATOR;
const DEBUG = true;

//根目录定义
define('ROOT_PATH', __DIR__ . DS);
define('LCD_PATH', ROOT_PATH . 'Lcd' . DS);
define('CACHE_PATH', ROOT_PATH . 'Cache' . DS);

//项目定义
define('CONFIG_PATH', ROOT_PATH . 'App' . DS . 'Config' . DS);
define('MODULE_PATH', ROOT_PATH . 'App' . DS . 'Module' . DS);

//缓存目录定义
define('DATA_CACHE_PATH', CACHE_PATH . 'Data' . DS);
define('LOG_CACHE_PATH', CACHE_PATH . 'Log' . DS);
define('SYSTEM_CACHE_PATH', CACHE_PATH . 'System' . DS);
define('TPL_CACHE_PATH', CACHE_PATH . 'Template' . DS);

header('Content-Type:text/html; charset=utf-8');

require LCD_PATH . 'Core' . DS . 'App.php';

//应用开始
Lcd\Core\App::run();