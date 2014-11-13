<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-19
 * Time: 下午4:21
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Database;

use Lcd\Core\Config;

abstract class Database {

    //数据库配置
    protected $config = array(
        'REGISTER'=>array(),//注册数据库集群
        'DB'=>array(),//数据库集

        'PREFIX' => '',//数据库表前缀
        'FIELDTYPE_CHECK' => false, //是否进行字段类型检查
        'FIELDS_CACHE' => true,//启用字段缓存
        'CHARSET' => 'utf8',//数据库编码默认采用utf8
        'SQL_BUILD_CACHE' => false, //数据库查询的SQL创建缓存
        'SQL_BUILD_QUEUE' => 'file', //SQL缓存队列的缓存方式 支持 file xcache和apc
        'SQL_BUILD_LENGTH' => 20, //SQL缓存的队列长度
        'SQL_LOG' => false, //SQL执行日志记录
        'BIND_PARAM' => false, //数据库写入数据自动参数绑定
    );

    //连接
    protected static $_link = array();

    //连接状态
    protected static $_linkState = array();

    //当前连接
    protected $link = null;

    // 当前查询ID
    protected $queryID = null;

    //受影响的行数
    protected $numRows = 0;

    //事务指令数
    protected $transTimes = 0;

    protected $lastInsID = 0;

    //读写分离
    protected $rwSeparate = true;

    /**
     * 取得数据库实例
     * @param string $dbName
     * @return Database
     */
    public final static function getInstance($dbName) {
        static $_db = array();

        if(!isset($_db['$dbName'])) {
            $_db['$dbName'] = self::factory($dbName);
        }

        return $_db['$dbName'];
    }


    /**
     * @param string $dbName
     * @return Database
     * @throws \Exception
     */
    public static function factory($dbName) {
        $_config = array();

        //初使化配置
        if(empty($_config)) {
            $_config = Config::block('Database');
        }

        //检查配置
        if(!isset($_config['DB'][$dbName])) {
            throw new \Exception("$dbName 数据库没有配置");
        }

        $config = $_config;
        $config['DB'] = $config['DB'][$dbName];

        if(!isset($config['REGISTER'][$config['DB']['CLUSTER']])) {
            throw new \Exception("$dbName 数据库配置不正确");
        }

        $config['REGISTER'] = $config['REGISTER'][$config['DB']['CLUSTER']];

        $classType = isset($config['REGISTER']['TYPE']) ? $config['REGISTER']['TYPE'] : $config['TYPE'];

        //类名
        $className = 'Lcd\\Database\\Driver\\' . ucwords($classType);

        if(!class_exists($className)) {
            throw new \Exception("$className 类不存在");
        }

        //实例化类
        return new $className($config);
    }

    /**
     * 初使化连接
     * @access protected
     * @param bool $master 是否主库
     * @return void
     * @throws \Exception
     */
    protected final function initConnect($master = true) {
        $cluster = $this->config['DB']['CLUSTER'] . ($master || !$this->rwSeparate ? '_master' : '_slave');

        //创建连接
        if(!isset(self::$_link[$cluster])) {
            $config = $this->config['REGISTER'];
            if($master) {
                $config['HOST'] = $config['HOST']['MASTER'];
            } elseif(!empty($config['HOST']['SLAVE'])) {
                $config['HOST'] = array_rand($config['HOST']['SLAVE']);
            } else {
                $this->rwSeparate = false;
                $this->initConnect(true);
                return;
            }
            self::$_link[$cluster] = $this->connect($config);
        }
        $this->link = self::$_link[$cluster];

        //设置DB
        if(!isset($this->config['DB']['DB_NAME']) || empty($this->config['DB']['DB_NAME'])) {
            throw new \Exception('数据库名不能为空');
        }
        if(!isset(self::$_linkState[$cluster]['DB_NAME']) || self::$_linkState[$cluster]['DB_NAME'] != $this->config['DB']['DB_NAME']) {
            $this->setDbName($this->config['DB']['DB_NAME'], $this->link);
        }

        //设置字符集
        if(!isset($this->config['DB']['CHARSET']) || empty($this->config['DB']['CHARSET'])) {
            $this->config['DB']['CHARSET'] = $this->config['CHARSET'];
        }
        if(!isset($this->config['DB']['CHARSET']) || empty($this->config['DB']['CHARSET'])) {
            throw new \Exception('字符集不能为空');
        }
        if(!isset(self::$_linkState[$cluster]['CHARSET']) || self::$_linkState[$cluster]['CHARSET'] != $this->config['DB']['CHARSET']) {
            $this->setCharset($this->config['DB']['CHARSET'], $this->link);
        }
    }

    /**
     * 获取数据库配置
     * @param string $name 配置名称
     * @return string
     */
    public final function config($name) {
        if(isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }

    /**
     * value分析
     * @access protected
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value) {
        if(is_string($value)) {
            $value =  '\''.$this->escapeString($value).'\'';
        }elseif(isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp'){
            $value =  $this->escapeString($value[1]);
        }elseif(is_array($value)) {
            $value =  array_map(array($this, 'parseValue'),$value);
        }elseif(is_bool($value)){
            $value =  $value ? '1' : '0';
        }elseif(is_null($value)){
            $value =  'null';
        }
        return $value;
    }

    /**
     * table分析
     * @access protected
     * @param mixed $tables
     * @return string
     */
    protected function parseTable($tables) {
        if(is_array($tables)) {// 支持别名定义
            $array = array();
            foreach ($tables as $table => $alias){
                if(!is_numeric($table)) {
                    $array[] = $this->parseKey($table).' '.$this->parseKey($alias);
                } else {
                    $array[] = $this->parseKey($table);
                }
            }
            $tables = $array;
        }elseif(is_string($tables)) {
            $tables  =  explode(',', $tables);
            array_walk($tables, array(&$this, 'parseKey'));
        }
        $tables = implode(',',$tables);
        return $tables;
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct() {
        // 释放查询
        if ($this->queryID){
            $this->free();
        }
        // 关闭连接
        $this->close();
    }

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

    /**
     * 连接数据库
     * @param array $config 连接配置
     * @return resource
     */
    abstract public function connect($config);

    /**
     * 设置DB
     * @param $dbName
     * @param resource $resource
     */
    abstract public function setDbName($dbName, $resource);

    /**
     * 设置字符集
     * @param $charset
     * @param resource $resource
     * @return void
     */
    abstract public function setCharset($charset, $resource);

    /**
     * 执行语句
     * @param $sql
     * @return mixed
     */
    abstract public function execute($sql);

    /**
     * 启动事务
     * @access public
     * @return void
     */
    abstract public function startTrans();

    /**
     * 用于非自动提交状态下面的查询提交
     * @access public
     * @return boolean
     */
    abstract public function commit();

    /**
     * 事务回滚
     * @access public
     * @return boolean
     */
    abstract public function rollback();

    /**
     * 替换记录
     * @access public
     * @param mixed $data 数据
     * @param array $options 参数表达式
     * @return false | integer
     */
    abstract public function replace($data, $options=array());

    /**
     * 查询
     * @param $sql
     * @return mixed
     */
    abstract public function query($sql);

    /**
     * 获得所有的查询数据
     * @access private
     * @return array
     */
    abstract public function getAll();

    /**
     * 取得数据表的字段信息
     * @access public
     * @param string $tableName
     * @return array
     */
    abstract public function getFields($tableName);

    /**
     * 取得数据库的表信息
     * @access public
     * @param string $dbName
     * @return array
     */
    abstract public function getTables($dbName='');

    /**
     * 插入记录
     * @access public
     * @param mixed $data 数据
     * @param array $options 参数表达式
     * @param boolean $replace 是否replace
     * @return false | integer
     */
    abstract public function insertAll($data, $options=array(), $replace=false);

    /**
     * 释放查询结果
     * @access public
     */
    abstract public function free();

    /**
     * 数据库错误信息
     * @access public
     * @return string
     */
    abstract public function error();

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */
    abstract public function escapeString($str);

    /**
     * 字段和表名处理添加`
     * @access protected
     * @param string $key
     * @return string
     */
    abstract public function parseKey(&$key);

    /**
     * 关闭数据库
     * @access protected
     * @return void
     */
    abstract public function close();
}