<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-2-23
 * Time: 下午4:44
 */

return array(

    //站点名
    'SITE_NAME'=>array('blog', 'value'),

    //模块名
    //默认,取值范围,键名
    'URL_CONFIG_ALIAS'=>array('index', 'domain', 'domain2'),//domain1|domain3|subDomain|pathInfo1|pathInfo2|pathInfo3|get[]|post[]

    //别名组
    'URL_CONFIG'=>array(
        'blog'=>array(
            'MODULE' => array('index','domain','domain2'),
            'CONTROLLER' => array('index','get','test'),
            'ACTION' => array('index','pathInfo','0'),
            'ACCOUNT' => array('index','domain','domain2'),
            'CACHE_PAGE'=>true
        ),
    ),
);

/**


Default = array('module','controller','action');
module = domain2
controller = domain3
action =

http://baike.baidu.com/view/134.html
http://baike.baidu.com/cat/test.html

http://fuzhuzheng.Blog.baidu.com/view/134.html

http://tuan.baidu.com/sz/cdasd.html

http://sz.baidu.com/view/134.html
http://sh.baidu.com/view/134.html


http://sz.baidu.com/
http://sh.baidu.com/

http://admin.baidu.com/   |   域名-->模块


http://fuzhuzheng.Blog.baidu.com/view/153.html
http://fuzhuzheng.Blog.baidu.com/admin/356.html


 */