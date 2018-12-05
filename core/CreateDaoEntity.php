<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BWB\Framework\mvc;

use ReflectionClass;

/**
 * Description of CreateDaoEntity
 *
 * @author alexandreplanque
 */
/**
 * Description of CreateDaoEntity
 * Attention NameSpace inscrit en dur
 * @author alexandreplanque
 */
class CreateDaoEntity extends DAO {

    private $namespace;
    
    public function __construct() {
        parent::__construct();
        $this->namespace = "BWB\\Framework\\mvc";
    }
/* /!\/!\/!\/!\/!\/!\/!\ NAMESPACE EN DUR /!\/!\/!\/!\/!\/!\ ____________________________________________________________*/
    public function createDaoEntity($table) {
        if(strpos($table,"_") !== FALSE){
            $temp = explode("_", $table);
            $filename = "";
            foreach($temp as $t){
                $filename .=  ucfirst(strtolower($t));
            }
        }else{
                $filename = ucfirst(strtolower($table));
            }
        $file = "<?php\nnamespace ".$this->namespace.";\nuse ".$this->namespace."\\DAO;\nuse ".$this->namespace."\\models\\" . ucfirst(strtolower($table)) . ";\n\n/* \n*creer avec l'objet issue de la classe CreateEntity Class \n*/\n\n\nclass DAO" . $filename . " extends DAO {\n\n\tpublic function __construct(){\n\t\tparent::__construct();\n\t}";
        $crud = $this->methodsName();
        $this->prepareCrud($file, $table);
        $this->prepareRepo($file, $table);

        $path = "./dao/DAO". $filename.".php";

        file_put_contents($path, $file);
        return $file;
    }


    public function getColumns($table) {
        $sql = "show columns from " . $table;
        $statement = $this->getPdo()->query($sql);
        $result = $statement->fetchAll();
        return $result;
    }

    public function getTable() {
        $config = json_decode(file_get_contents('../config/database.json'), true);
        $sql = "show tables from " . $config['dbname'];
        $statement = $this->getPdo()->query($sql);
        $result = $statement->fetchAll();
        return $result;
    }

    /* /!\/!\/!\/!\/!\/!\/!\ NAMESPACE EN DUR /!\/!\/!\/!\/!\/!\ ____________________________________________________________*/
    private function methodsName() {
        $crud = "BWB\\Framework\\mvc\\CRUDInterface";
        $reflex = new ReflectionClass($crud);
        return $reflex->getMethods();
    }

    /* /!\/!\/!\/!\/!\/!\/!\ NAMESPACE EN DUR /!\/!\/!\/!\/!\/!\ ________________________________________*/
    private function repoMethodsName() {
        $repository = "BWB\\Framework\\mvc\\RepositoryInterface";
        $reflex = new ReflectionClass($repository);
        return $reflex->getMethods();
    }

    /*
     * Méthode centralisant les 4 méthodes du CRUD de l'interface à implémenter
     * En fonction de la méthode courante dans le tableau, injection des données 
     * dans la méthode dédié à cette méthode
     * 
     *   $a = chaine de car a modif $table = nom de la table et donc de la classe   
     */

    private function prepareCrud(&$a, $table) {
        $a .= "\n\n/* ____________________Crud methods____________________*/";
        $methods = $this->methodsName();
        $props = $this->getColumns($table);
        $set = array();
        foreach ($props as $prop) {
            if ($prop[0] !== "id") {
                array_push($set, ucfirst($prop[0]));
            }
        }

        foreach ($methods as $method => $value) {
            $exp = explode(' ', $value);
            $arg = "$entity";
            if ($exp[6] === "create") {
                $a .= "\n\n\n\tpublic function " . $exp[6] . " (" . $exp[27] . "){";
                $a = $this->prepCreate($a, $table);
            } else if ($exp[6] === "delete") {
                $a .= "\n\n\n\tpublic function " . $exp[6] . " (\$id){";
                $a = $this->prepDelete($a, $table);
            } else if ($exp[6] === "retrieve") {
                $a .= "\n\n\n\tpublic function " . $exp[6] . " (\$id){";
                $a = $this->prepRetrieve($a, $table, $set);
            } elseif ($exp[6] === "update") {
                $a .= "\n\n\n\tpublic function " . $exp[6] . " (" . $exp[27] . "){";
                $a = $this->prepUpdate($a, $table, $set);
            }

            //$a .= "\n\n\n\tpublic function " . $exp[6] . " (" . $exp[27] . "){\n\n\t}";
        }
        return $a;
    }

    private function prepCreate($a, $table) {
        $props = $this->getColumns($table);
        $a .= "\n\n\t\t\$sql = \"INSERT INTO " . $table . " (";
        foreach ($props as $prop) {
            if ($prop[0] !== "id") {
                $a .= $prop[0] . ",";
            }
        }
        $a = substr($a, 0, -1);
        $a .= ") VALUES('\"";
        foreach ($props as $prop) {
            if ($prop[0] !== "id") {
                $a .= " . \$entity->get" . ucfirst($prop[0]) . "() . ','";
            }
        }

        $a = substr($a, 0, -6);
        $a .= " . \"')\";\n\t\t\$this->getPdo()->query(\$sql);\n\t}";
        return $a;
    }

    private function prepDelete(&$a, $table) {
        $a .= "\n\n\t\t\$sql = \"DELETE FROM " . $table . " WHERE id= \" . \$id;\n\t\t\$this->getPdo()->query(\$sql);\n\t}";
        return $a;
    }

    private function prepRetrieve(&$a, $table, $x) {
        $a .= "\n\n\t\t\$sql = \"SELECT * FROM " . $table . " WHERE id=\" . \$id;\n\t\t\$statement = \$this->getPdo()->query(\$sql);\n\t\t\$result = \$statement->fetch(PDO::FETCH_ASSOC);";
        $a .= "\n\t\t\$entity = new " . ucfirst(strtolower($table)) . "();\n\t\t";
        foreach ($x as $prop) {
            $a .= "\$entity->set" . $prop . "();\n\t\t";
        }
        $a .= "return \$entity;\n\t}";
        return $a;
    }

    private function prepUpdate(&$a, $table, $x) {
        $a .= "\n\n\t\t\$sql = \"UPDATE " . $table . " SET ";
        foreach ($x as $prop) {
            $a .= strtolower($prop) . " = '\" . \$entity->get" . $prop . "() .\"',";
        }
        $a = substr($a, 0, -4);
        $a .= ".\" WHERE id = \". \$entity->getId();\n\t\tif (\$this->getPdo()->exec(\$sql) !== 0){\n\t\t\techo \"Updated\";\n\t\t} else {\n\t\t\techo \"Failed\";\n\t\t}\n\t}";
        return $a;
    }
    
                /*   Partie concernant l'interface Repository_interface   */
    
    
    /*
     * Cette méthode à pour but de récuperer les données nécessaires au traitement de la chaîne de caractère
     * à injecter dans le fichier de destination et de renvoyer sur la méthode adéquat afin de 
     * clarifier la lecture du code.
     */
    private function prepareRepo(&$b, $table) {
        $b .= "\n\n/* ____________________Repository methods____________________*/";
        $methods = $this->repoMethodsName();
        $props = $this->getColumns($table);
        $set = array();
        foreach ($props as $prop) {
            array_push($set, $prop[0]);
        }
        foreach ($methods as $method => $value) {
            $exp = explode(' ', $value);

            if ($exp[6] === "getAll") {
                $b .= "\n\n\n\tpublic function " . $exp[6] . " (" . $exp[27] . "){";
                $b = $this->prepGetAll($b, $table, $set);
            } else if ($exp[6] === "getAllBy") {
                $b .= "\n\n\n\tpublic function " . $exp[6] . " (" . $exp[27] . "){";
                $b = $this->prepGetAllBy($b, $table, $set);
            }
        }
        return $b;
    }
    
    private function prepGetAll(&$b,$table, $prop){
        $b .= "\n\t\t\$sql = \"SELECT * FROM ".$table."\";\n\t\t\$statement = \$this->getPdo()->query(\$sql);\n\t\t\$results = \$statement->fetchAll();\n\t\t\$entities = array();\n\n\t\tforeach(\$results as \$result){\n\t\t\t\$entity = new ". ucfirst(strtolower($table))."();";
        foreach($prop as $p){
            $b .= "\n\t\t\t\$entity->set".ucfirst($p)."(\$result['".$p."']);";
        }
        $b .= "\n\t\t\tarray_push(\$entities,\$entity);\n\t\t}\n\t\treturn \$entities;\n\t}";
     return $b;   
    }
    
    private function prepGetAllBy(&$b,$table,$prop){
        $b .= "\n\t\t\$sql = \"SELECT * FROM ".$table."\";\n\t\t\$i = 0;\n\t\tforeach(\$filter as \$key => \$value){\n\t\t\tif(\$i===0){\n\t\t\t\t\$sql .= \" WHERE \";\n\t\t\t} else {\n\t\t\t\t\$sql .= \" AND \";\n\t\t\t}\n\t\t\t\$sql .= \$key . \" = \" . \$value . \"'\";\n\t\t\t\$i++;\n\t\t}";
        $b .= "\n\t\t\$entities = array();\n\t\t\$statement = \$this->getPdo()->query(\$sql);\n\t\t\$results = \$statement->fetchAll();\n\t\tforeach(\$results as \$result){\n\t\t\t\$entity = new ".ucfirst(strtolower($table)).";";
        foreach($prop as $p){
            $b .= "\n\t\t\t\$entity->set".ucfirst($p)."(\$result['".$p."']);";
        }
        $b .= "\n\t\t\tarray_push(\$entities,\$entity);\n\t\t}\n\t\treturn \$entities;\n\t}\n}";
        return $b;
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
