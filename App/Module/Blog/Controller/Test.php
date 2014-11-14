<?php
namespace Blog\Controller;

use Lcd\Controller\Controller;

class Test extends Controller{
    public function index(){
        echo 'blog test';exit;
    }

    public function test(){
        echo 'blog test test!!!';exit;
    }
}