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

require_once "vendor/autoload.php";

class Db {
    private $conn;
    private $manager;

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
        $config = Setup::createAnnotationMetadataConfiguration(array(ROOT_PATH."App/Entity"), DEBUG);

        $this->getConfig();

        $this->manager = EntityManager::create($this->conn, $config);
    }
}