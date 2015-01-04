<?php
namespace Blog\Controller;

use Lcd\Controller\Controller;
use Lcd\Doctrine\Doctrine;

class Test extends Controller{
    public function index(){
        echo 'blog test';exit;
    }

    public function test(){
        $db = new Doctrine();
        $manage = $db->getManager();

        $product = new \Product();

        //插入
        $product->setName('test1111');

        $manage->persist($product);

        $manage->flush();



        echo "Created Product with ID " . $product->getId() . "\n";

//        echo 'blog test test!!!';exit;
    }
}