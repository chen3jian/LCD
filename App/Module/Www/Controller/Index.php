<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-25
 * Time: 下午2:23
 * To change this template use File | Settings | File Templates.
 */

namespace Www\Controller;

use Lcd\Controller\Controller;
use Lcd\Core\Config;
use Lcd\Network\Response;


class Index extends Controller {
    private $json = array(
        'data'=>'data',
        'info'=>'info',
        'status'=>'status',
    );

    public function _initialize() {
        test();//函数调用
    }

    //首页
    public function index() {
        $this->assign('time',time());//模板渲染值
        $this->show('<h1>发送了</h1>');//直接输出内容
        $this->template('index');//调用当前index
        $this->template('Info/index');//调用Info文件夹下index页面
        $this->invokeAction('test');//调用test方法

    }

    //首页
    public function test() {
        $this->show('content');
        $this->template();
    }
}