<?php

namespace sprint\app\schemas;

use \sprint\app\core\Schema;
use \sprint\app\core\BluePrintSchema;

class Products extends Schema
{
    public function upgrade()
    {
        $this->create("product", function(BluePrintSchema $table)
        {
             $table->int("id")->autoIncreament()->primaryKey();
            
             $table->varchar("name");
            
             $table->decimal("price");
            
             $table->text("description");
            
             $table->dateTime("registered_at","CURRENT_TIMESTAMP");
            
             $table->dateTime("updated_at");
            
             $table->create();
         });
    }
    
    public function downgrade()
    {
        
    }
}