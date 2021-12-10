<?php

namespace sprint\app\entities;

use \sprint\app\core\Entity;
use \sprint\app\core\BluePrintEntity;

class E00001Product extends Entity
{
    public function upgrade()
    {
        $this->create("Product_test", function(BluePrintEntity $entity)
        {
             $entity->setAsProtected("product_id", "8")->db("product_id");
            
             $entity->setAsPrivate("product_fullname", "Test product")->db("product_fullname");
            
             $entity->setAsPrivate("product_username")->db("product_username");
            
             $entity->create();
         });
    }
}