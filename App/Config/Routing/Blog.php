<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-2-23
 * Time: 下午4:44
 */

return array(
    'MODULE' => array('blog'),
    //别名与控制器的映射
    'CONTROLLER_ACTION_MAP'=>array(
        'i'=>array(
            'alias'=>'Index',
            //别名与操作的映射
            'action'=>array(
                'i'=>'index',
                't'=>'test'
            )
        ),
        't'=>array(
            'alias'=>'Test',
            'action'=>array(
                'i'=>'index',
                't'=>'test'
            )
        ),
    ),
//    'CONTROLLER' => array('index','pathInfo','0'),
    'CONTROLLER' => array('i','pathInfo','0'),
//    'ACTION' => array('index','pathInfo','1'),
    'ACTION' => array('i','pathInfo','1'),
    'ACCOUNT' => array('index','domain','currentSubDomain'),
    'CACHE_PAGE'=>true
);