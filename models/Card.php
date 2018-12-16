<?php
namespace BWB\Framework\mvc\models;
/* 
*creer avec l'objet issue de la classe CreateEntity Class 
*/


Class Card {

		private $id;

		private $title;

		private $description;

		private $deadline;

		private $isdel;

		private $group_id;


/* ____________________ Getter and Setter Part ____________________ */


	public function getId (){
		return $this->id;
	}


	public function setId ($val){
		$this->id = $val;
	}


	public function getTitle (){
		return $this->title;
	}


	public function setTitle ($val){
		$this->title = $val;
	}


	public function getDescription (){
		return $this->description;
	}


	public function setDescription ($val){
		$this->description = $val;
	}


	public function getDeadline (){
		return $this->deadline;
	}


	public function setDeadline ($val){
		$this->deadline = $val;
	}


	public function getIsdel (){
		return $this->isdel;
	}


	public function setIsdel ($val){
		$this->isdel = $val;
	}


	public function getGroup_id (){
		return $this->group_id;
	}


	public function setGroup_id ($val){
		$this->group_id = $val;
	}

}