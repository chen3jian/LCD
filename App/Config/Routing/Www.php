<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-2-23
 * Time: 下午4:44
 */

return array(

    //站点名
    'SITE_NAME'=>array('www', 'value'),

    //模块名
    //默认,取值范围,键名
    /**
     * URL_CONFIG_ALIAS配置可能为以下几种情况：
     * 1.domain 获取配置中系统配置（System/Config.php）中的DOMAIN的配置
     * 2.pathInfo 获取系统环境变量中的相应路径信息，若为空，则取第1个元素
     * 3.get 取$_GET['domain2']，若该值为空，则取第1个元素
     * 4.post 取$_POST['domain2']，若该值为空，则取第1个元素
     * 5.其他，直接取第1个元素
     */
    'URL_CONFIG_ALIAS'=>array('index', 'domain', 'domain2'),//domain1|domain3|subDomain|pathInfo1|pathInfo2|pathInfo3|get[]|post[]
//    'URL_CONFIG_ALIAS'=>array('index', 'domain', 'domain2'),//domain1|domain3|subDomain|pathInfo1|pathInfo2|pathInfo3|get[]|post[]
//    'URL_CONFIG_ALIAS'=>array('index', 'get', 's'),//domain1|domain3|subDomain|pathInfo1|pathInfo2|pathInfo3|get[]|post[]
    'URL_CONFIG_ALIAS'=>array('www', 'pathInfo', 0),//domain1|domain3|subDomain|pathInfo1|pathInfo2|pathInfo3|get[]|post[]

    //别名组
    'URL_CONFIG'=>array(
        'www'=>array(
            'MODULE' => array('index','domain','domain2'),
            'CONTROLLER' => array('index','get','test'),
            'ACTION' => array('index','pathInfo','0'),
            'ACCOUNT' => array('index','domain','domain2'),
            'CACHE_PAGE'=>true
        ),
        'blog'=>array(
//            'MODULE' => array('index','get','s'),
            'MODULE' => array('index','pathInfo',0),
            'CONTROLLER' => array('index','pathInfo',1),
//            'CONTROLLER' => array('index','get','test'),
//            'ACTION' => array('index','get','0'),
            'ACTION' => array('index','pathInfo',2),
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