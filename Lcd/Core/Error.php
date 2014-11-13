<?php
/**
 * Created by PhpStorm.
 * User: fuzhuzheng
 * Date: 14-3-6
 * Time: 下午5:05
 */

namespace Lcd\Core;

use Lcd\Network\Request;

class Error {

    /**
     * 错误配置
     * @access private
     * @var array
     */
    private static $_config = array();

    /**
     * 日志初始化
     * @access public
     */
    private static function _init() {
        if(empty(self::$_config)) {
            self::$_config = Config::block('Error');
        }
    }

    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     * @return void
     */
    public static function appException($e) {
        //self::_init();
        $error = array();
        $error['message'] = $e->getMessage();
        $trace = $e->getTrace();
        if('E'==$trace[0]['function']) {
            $error['file'] = $trace[0]['file'];
            $error['line'] = $trace[0]['line'];
        } else {
            $error['file'] = $e->getFile();
            $error['line'] = $e->getLine();
        }
        $error['trace'] = $e->getTraceAsString();
        Log::record($error['message'], Log::ERR);
        // 发送404信息
        header('HTTP/1.1 404 Not Found');
        header('Status:404 Not Found');
        self::halt($error);
    }

    /**
     * 自定义错误处理
     * @access public
     * @param int $errNo 错误类型
     * @param string $errStr 错误信息
     * @param string $errFile 错误文件
     * @param int $errLine 错误行数
     * @return void
     */
    public static function appError($errNo, $errStr, $errFile, $errLine) {
        self::_init();
        switch ($errNo) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                ob_end_clean();
                $errorStr = "$errStr ".$errFile." 第 $errLine 行.";
                if(self::$_config['LOG_RECORD']) Log::write("[$errNo] ".$errorStr, Log::ERR);
                self::halt($errorStr);
                break;
            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                $errorStr = "[$errNo] $errStr ".$errFile." 第 $errLine 行.";
                self::trace($errorStr,'','NOTIC');
                break;
        }
    }

    /**
     * 致命错误捕获
     * @return void
     */
    public static function fatalError() {
        Log::save();
        if ($e = error_get_last()) {
            switch($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    ob_end_clean();
                    self::halt($e);
                    break;
            }
        }
    }

    /**
     * 错误输出
     * @param mixed $error 错误
     * @return void
     */
    public static function halt($error) {
        self::_init();
        $e = array();
        if(Config::read('APP_DEBUG') || Request::$isCli) {
            //调试模式下输出错误信息
            if (!is_array($error)) {
                $trace = debug_backtrace();
                $e['message'] = $error;
                $e['file'] = $trace[0]['file'];
                $e['line'] = $trace[0]['line'];
                ob_start();
                debug_print_backtrace();
                $e['trace'] = ob_get_clean();
            } else {
                $e = $error;
            }
            if(Request::$isCli) {
                exit($e['message'].PHP_EOL.'FILE: '.$e['file'].'('.$e['line'].')'.PHP_EOL.$e['trace']);
            }
        } else {
            //否则定向到错误页面
            $error_page = self::$_config['ERROR_PAGE'];
            if(!empty($error_page)) {
                redirect($error_page);
            } else {
                if ( self::$_config['SHOW_ERROR_MSG']) {
                    $e['message'] = is_array($error) ? $error['message'] : $error;
                } else {
                    $e['message'] = self::$_config['ERROR_MESSAGE'];
                }
            }
        }
        // 包含异常页面模板
        $TMPL_EXCEPTION_FILE =  self::$_config['TMPL_EXCEPTION_FILE'];
        if(!$TMPL_EXCEPTION_FILE) {
            //显示在加载配置文件之前的程序错误
            exit('<b>Error:</b>'.$e['message'].' in <b> '.$e['file'].' </b> on line <b>'.$e['line'].'</b>');
        }
        include "$TMPL_EXCEPTION_FILE";
        exit;
    }

    /**
     * 添加和获取页面Trace记录
     * @param string $value 变量
     * @param string $label 标签
     * @param string $level 日志级别(或者页面Trace的选项卡)
     * @param boolean $record 是否记录日志
     * @return void
     */
    public static function trace($value='', $label='', $level='DEBUG', $record=false) {
        self::_init();
        static $_trace =  array();

        $info = ($label?$label.':':'').print_r($value,true);
        if('ERR' == $level && self::$_config['TRACE_EXCEPTION']) {// 抛出异常
            throw new \Exception($info);
        }
        $level = strtoupper($level);
        if(!isset($_trace[$level]) || count($_trace[$level])>self::$_config['TRACE_MAX_RECORD']) {
            $_trace[$level] =   array();
        }
        $_trace[$level][]   =   $info;
        if(Request::$isAjax || !self::$_config['SHOW_PAGE_TRACE']  || $record) {
            Log::record($info,$level,$record);
        }
    }
}