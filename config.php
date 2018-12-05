<?php
use BWB\Framework\mvc\CreateEveryThings;
use BWB\Framework\mvc\CreateEntityClass;
use BWB\Framework\mvc\CreateDaoEntity;
require __DIR__ . '/vendor/autoload.php';


/* 
 * sudo chown alexandreplanque:www-data PhP/php-mvc.bwb/ -R
 * sudo find PhP/php-mvc.bwb/ -type d -exec chmod 776 {} \;
 * sudo find PhP/php-mvc.bwb/ -type f -exec chmod 660 {} \;
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if($argv[1] === "generate"){
    switch ($argv[2]) :
        case "all":
            (new CreateEveryThings())->prepareWorkspace();
            break;
        case "class":
            (new CreateEntityClass())->createFile($argv[3]);
            break;

        case "dao":
            (new CreateDaoEntity())->createDaoEntity($argv[3]);
            break;
        default:
            (new CreateDaoEntity())->createDaoEntity($argv[2]);
            (new CreateEntityClass())->createFile($argv[2]);
            break;
    endswitch;
        
}
