<?php

namespace sprint\app\core;

class Entity
{    
    public function create(String $entity, callable $callback)
    {
        call_user_func($callback, new BluePrintEntity($entity));
    }
    
}