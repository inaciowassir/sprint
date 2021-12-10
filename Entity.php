<?php

namespace sprint;

use \sprint\app\core\BluePrintEntity;

class Entity
{
    public function __construct()
    {        
        $dir = __DIR__."/app/entities/";        
        $files = scandir($dir);  
        
        foreach($files as $file)
        {
            if($file === "." || $file === ".." || $file === "entity")
            {
                continue;
            }
            
            $entity = pathinfo($file, PATHINFO_FILENAME);
            
            $className = "\\sprint\\app\\entities\\{$entity}";

            $newClassEntity = new $className();

            $newClassEntity->upgrade();
        }
    }
}