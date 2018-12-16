<?php
namespace BWB\Framework\mvc;
use BWB\Framework\mvc\DAO;
use BWB\Framework\mvc\models\Card;

/* 
*creer avec l'objet issue de la classe CreateEntity Class 
*/


class DAOCard extends DAO {

	public function __construct(){
		parent::__construct();
	}

/* ____________________Crud methods____________________*/


	public function create ($array){

		$sql = "INSERT INTO card (title,description,deadline,isdel,group_id) VALUES('" . $entity->getTitle() . ',' . $entity->getDescription() . ',' . $entity->getDeadline() . ',' . $entity->getIsdel() . ',' . $entity->getGroup_id() . "')";
		$this->getPdo()->query($sql);
	}


	public function retrieve ($id){

		$sql = "SELECT * FROM card WHERE id=" . $id;
		$statement = $this->getPdo()->query($sql);
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		$entity = new Card();
		$entity->setTitle();
		$entity->setDescription();
		$entity->setDeadline();
		$entity->setIsdel();
		$entity->setGroup_id();
		return $entity;
	}


	public function update ($array){

		$sql = "UPDATE card SET title = '" . $entity->getTitle() ."',description = '" . $entity->getDescription() ."',deadline = '" . $entity->getDeadline() ."',isdel = '" . $entity->getIsdel() ."',group_id = '" . $entity->getGroup_id() ." WHERE id = ". $entity->getId();
		if ($this->getPdo()->exec($sql) !== 0){
			echo "Updated";
		} else {
			echo "Failed";
		}
	}


	public function delete ($id){

		$sql = "DELETE FROM card WHERE id= " . $id;
		$this->getPdo()->query($sql);
	}

/* ____________________Repository methods____________________*/


	public function getAll (){
		$sql = "SELECT * FROM card";
		$statement = $this->getPdo()->query($sql);
		$results = $statement->fetchAll();
		$entities = array();

		foreach($results as $result){
			$entity = new Card();
			$entity->setId($result['id']);
			$entity->setTitle($result['title']);
			$entity->setDescription($result['description']);
			$entity->setDeadline($result['deadline']);
			$entity->setIsdel($result['isdel']);
			$entity->setGroup_id($result['group_id']);
			array_push($entities,$entity);
		}
		return $entities;
	}


	public function getAllBy ($filter){
		$sql = "SELECT * FROM card";
		$i = 0;
		foreach($filter as $key => $value){
			if($i===0){
				$sql .= " WHERE ";
			} else {
				$sql .= " AND ";
			}
			$sql .= $key . " = " . $value . "'";
			$i++;
		}
		$entities = array();
		$statement = $this->getPdo()->query($sql);
		$results = $statement->fetchAll();
		foreach($results as $result){
			$entity = new Card;
			$entity->setId($result['id']);
			$entity->setTitle($result['title']);
			$entity->setDescription($result['description']);
			$entity->setDeadline($result['deadline']);
			$entity->setIsdel($result['isdel']);
			$entity->setGroup_id($result['group_id']);
			array_push($entities,$entity);
		}
		return $entities;
	}
}