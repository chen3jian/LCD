<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-25
 * Time: 下午2:23
 * To change this template use File | Settings | File Templates.
 */

namespace Mobile\Controller;

use Lcd\Controller\Controller;

class Index extends Controller {

    //首页
    public function index() {
        $this->assign('content','test_' . date('Y-m-d H:i:s'));
        return $this->template();
    }

    //首页
    public function test() {
        $this->assign('content','test_' . date('Y-m-d H:i:s'));
        $this->template();
    }
}