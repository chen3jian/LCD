<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-18
 * Time: 下午4:42
 * To change this template use File | Settings | File Templates.
 */
namespace Lcd\Core;

class Config {

    /**
     * 所有配置内容
     * @var array
     */
    static $_config = array();

    /**
     * 写系统配置
     * @param string|array $config
     * @param string $value
     * @return bool
     */
    public final static function write($config, $value = null) {
        if(!is_array($config)) {
            $config = array($config => $value);
        }
        self::$_config = array_merge(self::$_config, $config);
        return true;
    }

    /**
     * 读系统配置
     * @param null $name
     * @return string
     */
    public final static function read($name = null) {
        if(isset(self::$_config[$name])) {
            return self::$_config[$name];
        }
        return null;
    }

    /**
     * 获取配置块的数据
     * @return array
     */
    public final static function block() {
        $args = func_get_args();

        $args = array_map('ucfirst', $args);

        //默认配置
        if(func_num_args()<=1) {
            $args[] = 'Config';
        }

        $configFilePath = CONFIG_PATH . implode(DS, $args) . '.php';

        if(is_file($configFilePath)) {
            $_config = include "$configFilePath";
        }

        if(empty($_config) || !is_array($_config))
            return array();

        //系统配置
        if($args[0] = 'System') {
            self::write($_config);
        }

        return $_config;
    }

    /**
     * 读取模块配置
     * @param string $module
     * @param string $args
     * @return string
     */
    public final static function __callStatic($module, $args) {
        $group = '__group_' . $module;
        $name = $args[0];
        //优先返回数据
        if(isset(self::$_config[$group][$name])) {
            return self::$_config[$group][$name];
        }

        //这里加模模块配置
        if(!isset(self::$_config[$group])) {
            $configFilePath = MODULE_PATH . ucfirst($module) . DS . 'Config' . DS . 'Config.php';
            if(is_file($configFilePath)) {
                self::$_config[$group] = include "$configFilePath";
            } else {
                self::$_config[$group] = null;
            }
        }

        if(!isset(self::$_config[$group][$name])) {
            self::$_config[$group][$name] = null;
        }
        return self::$_config[$group][$name];
    }

    /**
     * 检测
     * @param string $name
     * @return bool
     */
    public final static function check($name = null) {
        return isset(self::$_config[$name]);
    }

    /**
     * 删除
     * @param string $name
     * @return void
     */
    public final static function delete($name = null) {
        if(isset(self::$_config[$name])) {
            unset(self::$_config[$name]);
        }
    }
}