<?php

namespace sprint\app\core;

class Schema
{    
    public function create(String $tableName, callable $callback)
    {
        $bluePrintSchema = new BluePrintSchema();
        
        $bluePrintSchema->setTable($tableName);
        
        call_user_func($callback, $bluePrintSchema);
    }
}