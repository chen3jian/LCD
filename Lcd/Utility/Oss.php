<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-3-18
 * Time: 下午3:04
 */

namespace Lcd\Utility;

use Aliyun\OSS\OSSClient;
use Lcd\Core\Config;
use Symfony\Component\ClassLoader\UniversalClassLoader;

require_once __DIR__ . '/OssLib/libs/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
    'Guzzle\\Common' => __DIR__.'/OssLib/libs/guzzle/common',
    'Guzzle\\Parser' => __DIR__.'/OssLib/libs/guzzle/parser',
    'Guzzle\\Plugin' => __DIR__.'/OssLib/libs/guzzle/plugin',
    'Guzzle\\Stream' => __DIR__.'/OssLib/libs/guzzle/stream',
    'Guzzle\\Http' => __DIR__.'/OssLib/libs/guzzle/http',
    'Symfony\\Component\\EventDispatcher' => __DIR__.'/OssLib/libs/symfony/event-dispatcher',
    'Symfony\\Component\\ClassLoader' => __DIR__.'/OssLib/libs/symfony/class-loader',
    'Aliyun' => __DIR__.'/OssLib/src',
));

$loader->register();

class Oss extends OSSClient {

    /**
     * Oss 配置
     * @var array
     */
    public static $config = array();

    public static function factory(array $config = array()) {
        if(empty($config)) {
            $config = Config::block('Oss');
        }

        //加载配置
        self::$config = $config;

        unset(self::$config['AccessKeyId'],self::$config['AccessKeySecret'],self::$config['Endpoint']);

        return new static(array(
            'AccessKeyId'=>$config['AccessKeyId'],
            'AccessKeySecret'=>$config['AccessKeySecret'],
            'Endpoint'=>$config['Endpoint']
        ));
    }
}