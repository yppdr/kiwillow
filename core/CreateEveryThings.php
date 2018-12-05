<?php

use BWB\Framework\mvc\Dao;
use BWB\Framework\mvc\CreateDaoEntity;
use BWB\Framework\mvc\CreateEntityClass;


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace BWB\Framework\mvc;


/**
 * Description of createEveryThings
 *
 * @author alexandreplanque
 */
class CreateEveryThings extends DAO {
    private $dbName;
    
    public function __construct() {
        parent::__construct();
        $this->getDbname();
    }
    
    private function getDbname(){
        $config = json_decode(file_get_contents('./config/database.json'),true);
        $this->dbName = $config['dbname'];
    }
    
    public function prepareWorkspace(){
        $tables = $this->getTable();
        foreach($tables as $table){
            $dao = new CreateDaoEntity();
            $dao->createDaoEntity($table);
            $entity = new CreateEntityClass();
            $entity->createFile($table);
        }
        
    }
    
    private function getTable(){
        $sql = "show tables from ".$this->dbName;
        $statement = $this->getPdo()->query($sql);
        $result = $statement->fetchAll();
        $tables = array();
        foreach($result as $r){
            array_push($tables,$r[0]);
        }
        return $tables;
    }
    public function create($array) {
        
    }

    public function delete($id) {
        
    }

    public function retrieve($id) {
        
    }

    public function update($array) {
        
    }

    public function getAll() {
        
    }

    public function getAllBy($filter) {
        
    }

}
