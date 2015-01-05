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
//        $manage = $db->getManager();

        $product = new \Product();

        //插入
//        $product->setName('test11112');

//        $db->persist($product);

        $list = $db->findAll('Product');

        $db->flush();

        var_dump($list);

//        echo "Created Product with ID " . $product->getId() . "\n";

//        echo 'blog test test!!!';exit;
    }
}