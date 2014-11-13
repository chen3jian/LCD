<?php
/**
 * Created by PhpStorm.
 * User: fuzhuzheng
 * Date: 14-3-6
 * Time: 上午11:27
 */
return array(

    //---------------------------------------------------------------------
    //默认配置
    'TYPE'=>'Mysql',
    'PREFIX' => '',//数据库表前缀
    'FIELDTYPE_CHECK' => false, //是否进行字段类型检查
    'FIELDS_CACHE' => true,//启用字段缓存
    'CHARSET' => 'utf8',//数据库编码默认采用utf8
    'SQL_BUILD_CACHE' => false, //数据库查询的SQL创建缓存
    'SQL_BUILD_QUEUE' => 'file', //SQL缓存队列的缓存方式 支持 file xcache和apc
    'SQL_BUILD_LENGTH' => 20, //SQL缓存的队列长度
    'SQL_LOG' => false, //SQL执行日志记录
    'BIND_PARAM' => false, //数据库写入数据自动参数绑定

    //---------------------------------------------------------------------
    //注册数据库集群
    'REGISTER'=>array(
        //集群1
        'CLUSTER_1'=>array(//IP,端口,账号,密码
            'TYPE'=>'Mysql',
            'HOST'=>array(
                'MASTER'=>'127.0.0.1',
                'SLAVE'=>array(
                    '127.0.0.2',
                    '127.0.0.3'
                )
            ),
            'PORT'=>'3306',
            'USER'=>'root',
            'PWD'=>'2008',
        ),
        //集群2
        'CLUSTER_2'=>array(//IP,端口,账号,密码
            'HOST'=>array(
                'MASTER'=>'127.0.0.1',
                'SLAVE'=>array(
                    '127.0.0.2',
                    '127.0.0.3'
                )
            ),
            'PORT'=>'3306',
            'USER'=>'root',
            'PWD'=>'2008',
        ),
    ),

    //---------------------------------------------------------------------
    //数据库集
    'DB'=>array(
        'db1'=>array(
            'CLUSTER'=>'',
            'DB_NAME' => '',//数据库名
            'PREFIX' => '',//数据库表前缀
        ),

        'db2'=>array(
            'CLUSTER'=>'',
            'DB_NAME' => '',//数据库名
            'PREFIX' => '',//数据库表前缀
        ),

        'db3'=>array(
            'CLUSTER'=>'',
            'DB_NAME' => '',//数据库名
            'PREFIX' => '',//数据库表前缀
        ),

        'db4'=>array(
            'CLUSTER'=>'',
            'DB_NAME' => '',//数据库名
            'PREFIX' => '',//数据库表前缀
        ),

        'db5'=>array(
            'CLUSTER'=>'',
            'DB_NAME' => '',//数据库名
            'PREFIX' => '',//数据库表前缀
        ),
    ),
);