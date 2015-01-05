<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dragon
 * Date: 15-1-4
 * Time: 下午12:41
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\Doctrine;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Lcd\Core\Config;

//require_once "vendor/autoload.php";

class Doctrine {
    private $conn;
    private $manager;
    private $repository;

    function __construct(){
        $this->init();
    }

    public function getManager(){
        return $this->manager;
    }

    private function getConfig(){
        if(!$this->conn){
            $this->conn = Config::block('Doctrine');
            if(empty($this->conn)){
                throw new \Exception('数据库配置不能为空');
            }
        }
    }

    private function init(){
        //配置类实例，所有配置,缓存处理
        $config = Setup::createAnnotationMetadataConfiguration(array(ROOT_PATH."App/Entity"), DEBUG);

//        $config = Setup::createConfiguration(DEBUG);

        $this->getConfig();//获取连接数据库的相关配置

        $this->manager = EntityManager::create($this->conn, $config);
    }

    /**
     * @param $entity
     * 获取实体存储库类实例
     */
    private function getRepository($entityClassName){
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

    private function dealEntityName(&$entityName){
        if($entityName[0]=='\\'){
            $entityName = substr($entityName,1);
        }
    }

    public function find($entityName,$id){
        $this->dealEntityName($entityName);
//        echo $entityName;exit;
        $detail =  $this->manager->find($entityName,$id);

        $this->flush();

        return $detail;
    }

    public function findAll($entityName){
        $this->dealEntityName($entityName);
//        echo $entityName;exit;
        $repostory = $this->getRepository($entityName);

        $all = $repostory->findAll();

        $this->flush();

        return $all;
    }

    public function delete($entityName,$id){
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

    public function flush(){
        $this->manager->flush();
    }
}