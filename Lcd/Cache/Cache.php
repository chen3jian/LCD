<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-19
 * Time: 下午5:58
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Cache;

 class Cache {
    //缓存实例列表
    protected static $_instance = array();

    public static function __callStatic($type, $args) {
        $className = 'Lcd\\Cache\\Engine\\' . ucfirst($type) . 'Engine';

        //创建实例
        if(!isset($_cache[$className])) {
            if(class_exists($className)) {
                self::$_instance[$className] = new $className();
            } else {
                throw new \Exception('缓存类不存在');
            }
        }

        //直接返回连接对象
        if($args[0]===true && !empty(self::$_instance[$className])) {
            return self::$_instance[$className]->_cache;
        }

        return self::$_instance[$className];
    }

}