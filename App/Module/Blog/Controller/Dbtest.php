<?php
namespace Blog\Controller;
use Lcd\Controller\Controller;
use Lcd\Doctrine\Doctrine;

/**
 * Created by JetBrains PhpStorm.
 * User: Dragon
 * Date: 15-1-5
 * Time: 下午10:06
 * To change this template use File | Settings | File Templates.
 */
class Dbtest extends Controller{
    private $entityName = 'Entity\\Product';
    private $db;
    private $entity;

    protected function _initialize(){
        $this->db = new Doctrine();
        $this->entity = new $this->entityName;
    }

    public function all(){
        //DQL查询
//        $query = $this->db->createQuery("select p from \Product p");
//        $list = $query->getResult();

        //SQL查询
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult('\Product', 'p');
        $rsm->addFieldResult('p', 'id', 'id');
        $rsm->addFieldResult('p', 'name', 'name');
        $query = $this->db->createNativeQuery("SELECT * FROM products",$rsm);
        $list = $query->getResult();

//        $list = $this->db->findAll($this->entityName);

        var_dump($list);
    }

    public function detail(){
        $id = 1;

        $detail = $this->db->find($this->entityName,$id);

        var_dump($detail);
    }

    public function add(){

        $this->entity->setName('你好啊');
        $this->db->persist($this->entity);

        echo "Created Product with ID " . $this->entity->getId() . "<br/>";
    }

    public function update(){
        $id = 1;
        $newName = 'ORM_new1';

        $product = $this->db->find('Product', $id);
//        var_dump($product);exit;
        if ($product === null) {
            echo "Product $id does not exist.<br/>";
            exit(1);
        }

        $product->setName($newName);

        $this->db->flush();
    }

    public function del(){
        $id = 8;

        $this->db->deleteById($this->entityName,$id);

//        var_dump($res);exit;
    }
}