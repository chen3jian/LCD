<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-3-14
 * Time: 上午10:51
 */

namespace Lcd\Database\Driver;

use Lcd\Database\Database;
use Lcd\Database\Driver;

class Mysql extends Database {

    /**
     * 架构方法
     * @param $config
     * @throws \Exception;
     */
    public function __construct($config){
        if (!extension_loaded('mysql')) {
            throw new \Exception("mysql 没有安装");
        }
        if(empty($config)) {
            throw new \Exception("不能没有配置");
        }
        $this->config = $config;
    }

    /**
     * 连接数据库
     * @param array $config 连接配置
     * @return resource
     */
    public final function connect($config) {

        //放弃了长连接(容易出错)
        $db = mysql_connect($config['HOST'], $config['USER'], $config['PWD'], true, 131072);

        return $db;
    }

    /**
     * 设置DB
     * @param $dbName
     * @param resource $resource
     * @return void
     */
    public final function setDbName($dbName, $resource) {
        mysql_select_db($dbName, $resource);
    }

    /**
     * 设置字符集
     * @param string $charset 数据库字符集
     * @param resource $resource
     * @return void
     */
    public final function setCharset($charset, $resource) {
        if(empty($charset)) {
            $charset = $this->config['CHARSET'];
        }
        mysql_query("SET NAMES '".$charset."'", $resource);
    }

    /**
     * 执行语句
     * @param $sql
     * @return mixed
     */
    public function execute($sql) {
        $this->initConnect(true);
        if (!$this->link)
            return false;

        //释放前次的查询结果
        if ($this->queryID)
            $this->free();

        $result = mysql_query($sql, $this->link) ;

        if(false === $result) {
            $this->error();
            return false;
        } else {
            $this->numRows = mysql_affected_rows($this->link);
            $this->lastInsID = mysql_insert_id($this->link);
            return $this->numRows;
        }
    }

    /**
     * 启动事务
     * @access public
     * @return mixed
     */
    public function startTrans() {
        $this->initConnect(true);
        if(!$this->link)
            return false;
        //数据rollback 支持
        if ($this->transTimes == 0) {
            mysql_query('START TRANSACTION', $this->link);
        }
        $this->transTimes++;
        return null;
    }

    /**
     * 用于非自动提交状态下面的查询提交
     * @access public
     * @return boolean
     */
    public function commit() {
        if ($this->transTimes > 0) {
            $result = mysql_query('COMMIT', $this->link);
            $this->transTimes = 0;
            if(!$result){
                $this->error();
                return false;
            }
        }
        return true;
    }

    /**
     * 事务回滚
     * @access public
     * @return boolean
     */
    public function rollback() {
        if ($this->transTimes > 0) {
            $result = mysql_query('ROLLBACK', $this->link);
            $this->transTimes = 0;
            if(!$result){
                $this->error();
                return false;
            }
        }
        return true;
    }

    /**
     * 替换记录
     * @access public
     * @param mixed $data 数据
     * @param array $options 参数表达式
     * @return false | integer
     */
    public function replace($data, $options=array()) {
        $values = array();
        $fields = array();
        foreach($data as $key=>$val) {
            $value = $this->parseValue($val);
            if(is_scalar($value)) { // 过滤非标量数据
                $values[] = $value;
                $fields[] = $this->parseKey($key);
            }
        }
        $sql = 'REPLACE INTO '.$this->parseTable($options['table']).' ('.implode(',', $fields).') VALUES ('.implode(',', $values).')';
        return $this->execute($sql);
    }

    /**
     * 查询
     * @param $sql
     * @return mixed
     */
    public function query($sql) {
        $this->initConnect(false);

        if (!$this->link)
            return false;

        //释放前次的查询结果
        if ($this->queryID)
            $this->free();

        $this->queryID = mysql_query($sql, $this->link);

        if (false === $this->queryID) {
            $this->error();
            return false;
        } else {
            $this->numRows = mysql_num_rows($this->queryID);
            return $this->getAll();
        }
    }

    /**
     * 获得所有的查询数据
     * @access private
     * @return array
     */
    public function getAll() {
        //返回数据集
        $result = array();
        if($this->numRows >0) {
            while($row = mysql_fetch_assoc($this->queryID)){
                $result[]   =   $row;
            }
            mysql_data_seek($this->queryID, 0);
        }
        return $result;
    }

    /**
     * 取得数据表的字段信息
     * @access public
     * @param string $tableName
     * @return array
     */
    public function getFields($tableName) {
        $result =   $this->query('SHOW COLUMNS FROM '.$this->parseKey($tableName));
        $info   =   array();
        if($result) {
            foreach ($result as $val) {
                $info[$val['Field']] = array(
                    'name'    => $val['Field'],
                    'type'    => $val['Type'],
                    'notnull' => (bool) (strtoupper($val['Null']) === 'NO'), // not null is empty, null is yes
                    'default' => $val['Default'],
                    'primary' => (strtolower($val['Key']) == 'pri'),
                    'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
                );
            }
        }
        return $info;
    }

    /**
     * 取得数据库的表信息
     * @access public
     * @param string $dbName
     * @return array
     */
    public function getTables($dbName='') {
        if(!empty($dbName)) {
            $sql    = 'SHOW TABLES FROM '.$dbName;
        }else{
            $sql    = 'SHOW TABLES ';
        }
        $result =   $this->query($sql);
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }

    /**
     * 插入记录
     * @access public
     * @param mixed $_data 数据
     * @param array $options 参数表达式
     * @param boolean $replace 是否replace
     * @return false | integer
     */
    public function insertAll($_data, $options=array(), $replace=false) {
        if(!is_array($_data[0]))
            return false;
        $fields = array_keys($_data[0]);
        array_walk($fields, array($this, 'parseKey'));
        $values = array();
        foreach($_data as $data) {
            $value = array();
            foreach($data as $val) {
                $val = $this->parseValue($val);
                if(is_scalar($val)) { //过滤非标量数据
                    $value[] = $val;
                }
            }
            $values[] = '('.implode(',', $value).')';
        }
        $sql = ($replace?'REPLACE':'INSERT').' INTO '.$this->parseTable($options['table']).' ('.implode(',', $fields).') VALUES '.implode(',',$values);
        return $this->execute($sql);
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $sql  SQL字符串
     * @return string
     */
    public function escapeString($sql) {
        if($this->link) {
            return mysql_real_escape_string($sql, $this->link);
        }else{
            return mysql_escape_string($sql);
        }
    }

    /**
     * 字段和表名处理添加`
     * @access protected
     * @param string $key
     * @return string
     */
    public function parseKey(&$key) {
        $key = trim($key);
        if(!preg_match('/[,\'\"\*\(\)`.\s]/',$key)) {
            $key = '`' . $key . '`';
        }
        return $key;
    }

    /**
     * 数据库错误信息
     * @access public
     * @return string
     */
    public function error() {

    }

    /**
     * 释放查询结果
     * @access public
     */
    public function free() {
        mysql_free_result($this->queryID);
        $this->queryID = null;
    }

    /**
     * 关闭数据库
     * @access protected
     * @return void
     */
    public function close() {
        if($this->link) {
            mysql_close($this->link);
        }
        $this->link = null;
    }
}