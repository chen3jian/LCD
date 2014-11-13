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
use Lcd\Network\Response;

class Index extends Controller {
    private $json = array(
        'data'=>'data',
        'info'=>'info',
        'status'=>'status',
    );

    public function _initialize() {
        //return 'test';
    }

    //首页
    public function index() {
//        $this->assign('data', 'a');
//        $this->assign('info', 'b');
//        $this->assign('status', 'c');
//        $this->assign('_jsonp','jp');
        //return $this->json($this->json);

        return $this->template();
    }

    //首页
    public function test() {
        $this->assign('content','test_' . date('Y-m-d H:i:s'));
        $this->template();
    }
}