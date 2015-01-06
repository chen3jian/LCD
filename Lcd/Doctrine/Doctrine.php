<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dragon
 * Date: 15-1-4
 * Time: 下午12:41
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Doctrine;

use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Lcd\Core\Config;

/**
 * 数据库操作入口文件
 * Class Doctrine
 * @package Lcd\Doctrine
 */
class Doctrine extends EntityManagerDecorator {
    private $conn;
    private $manager;
    private $repository;
    private $entityName;

    function __construct(){
        $this->init();
        parent::__construct($this->manager);
    }

    /**
     * 获取实体管理类实例对象
     * @return mixed
     */
    public function getManager(){
        return $this->manager;
    }

    /**
     * 获取数据库连接配置
     * @throws \Exception
     */
    private function getConfig(){
        if(!$this->conn){
            $this->conn = Config::block('Doctrine');
            if(empty($this->conn)){
                throw new \Exception('数据库配置不能为空');
            }
        }
    }

    /**
     * 数据库初始化
     */
    private function init(){
        //配置类实例，所有配置,缓存处理
        $config = Setup::createAnnotationMetadataConfiguration(array(ROOT_PATH."App/Entity"), DEBUG);

        $this->getConfig();//获取连接数据库的相关配置

        $this->manager = EntityManager::create($this->conn, $config);
    }

    /**
     * @param $entity
     * 获取实体存储库类实例
     */
    private function getEntityRepository($entityClassName){
        if(empty($entityClassName)) return;
        static $repository = array() ;
        $key = md5($entityClassName);
        if(!$repository[$key]){
            $repository[$key] = $this->manager->getRepository($entityClassName);
        }
        return $repository[$key];
    }

    public function persist($entity){
        $this->manager->persist($entity);

        $this->flush();
    }

    /**
     * 实体类名处理
     * @param $entityName
     */
    private function dealEntityName(&$entityName){
        if($entityName[0]=='\\'){
            $entityName = substr($entityName,1);
        }
    }

    /**
     * 查询表中所有数据
     * @param $entityName
     * @return mixed
     */
    public function findAll($entityName){
        $this->dealEntityName($entityName);
        $repostory = $this->getEntityRepository($entityName);

        $all = $repostory->findAll();

        $this->flush();

        return $all;
    }

    /**
     * 根据ID进行删除
     * @param $entityName
     * @param $id
     * @return bool
     */
    public function deleteById($entityName,$id){
        $this->dealEntityName($entityName);

        $entity = $this->manager->find($entityName, $id);
//        var_dump($entity);exit;
        if($entity === null){
            return false;
        }

        $this->manager->remove($entity);

        $this->flush();

        return true;
    }

    /**
     * 删除实体类对象——删除数据库中匹配该对象的相关数据
     * @param $entity实体类对象
     */
    public function delete($entity){
        $this->manager->remove($entity);
    }

    /**
     * 把数据更新到数据库中，并清空连接缓存
     */
    public function flush(){
        $this->manager->flush();
    }

    /**
     * 开启事务
     */
    public function startTrans(){
        parent::beginTransaction();
    }
}