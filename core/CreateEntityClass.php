<?php

namespace BWB\Framework\mvc;

use ReflectionClass;



/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CreateEntityClass
 * Permet de créer une classe qui découle d'une table de la base de données, le DAO
 * est requis pour cette classe
 * Exemple d'utilisation : 
 * $object = new CreateEntityClass();
 * $object->createFile($nomDeLaTable);use BWB\Framework\mvc\Crud_interface;
use BWB\Framework\mvc\Dao;
 * Pensez à vérifier que vous avez gérer les permissions pour écrire au travers d'un script
 * 
 * au besoin voici les lignes de commandes a effectuer (les lignes de commandes sont faites dans le dossier ServeurWeb: 
 * su administrateur
 * sudo chown alexandreplanque:www-data PhP/php-mvc.bwb/ -R
 * sudo find PhP/php-mvc.bwb/ -type d -exec chmod 776 {} \;
 * sudo find PhP/php-mvc.bwb/ -type f -exec chmod 660 {} \;
 * @author alexandreplanque
 */
class CreateEntityClass extends DAO {

    private $namespace;

    public function __construct() {
        parent::__construct();
        $this->namespace = "BWB\\Framework\\mvc\\models;";
    }

    private function getColumns($table) {
        $sql = "show columns from " . $table;
        $statement = $this->getPdo()->query($sql);
        $result = $statement->fetchAll();
        ///var_dump($result);
        return $result;
    }

    public function createFile($table) {
        $columns = $this->getColumns($table);
        if (strpos($table, "_") !== FALSE) {
            $temp = explode("_", $table);
            $filename = "";
            foreach ($temp as $t) {
                $filename .= ucfirst(strtolower($t));
            }
        } else {
            $filename = ucfirst(strtolower($table));
        }

        $file = "<?php\nnamespace " . $this->namespace . "\n/* \n*creer avec l'objet issue de la classe CreateEntity Class \n*/\n\n\nClass " . $filename . " {";
        $getSet = "";
        
        foreach ($columns as $col) {

            $file .= "\n\n\t\tprivate $" . $col[0] . ";";
            $getSet .= "\n\n\n\tpublic function get" . ucfirst($col[0]) . " (){\n\t\treturn \$this->" . $col[0] . ";\n\t}\n\n\n\tpublic function set" . ucfirst($col[0]) . " (\$val){\n\t\t\$this->" . $col[0] . " = \$val;\n\t}";
        }

        $path = "./models/" . $filename . ".php";
        $file .= "\n\n\n/* ____________________ Getter and Setter Part ____________________ */" . $getSet . "\n\n}";
        file_put_contents($path, $file);
    }

    private function methodsName() {
        $reflex = new ReflectionClass(Crud_interface);
        return $reflex->getMethods();
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
