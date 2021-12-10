<?php

namespace sprint;

use \sprint\app\core\Model;
use \sprint\app\core\BluePrintSchema;

class Schema extends Model
{
    private $schemaTableName = "schema_version";
    
    public function __construct()
    {
        parent::__construct();
        
        $dir = __DIR__."/app/schemas/";
        
        $files = scandir($dir);
        
        $this->schemaTable();
        
        $savedSchema = $this->select($this->schemaTableName)->columns("schema_name")->results();
        
        $savedSchema = array_column($savedSchema, "schema_name");
        
        $toBeSavedSchema = array_diff($files, $savedSchema);
        
        foreach($toBeSavedSchema as $schema)
        {
            if($schema == "." || $schema == "..")
            {
                continue;
            }
            
            $this->insert($this->schemaTableName)->values(array(
                "schema_name" => $schema
            ));
            
            if($this->insert_id() > 0)
            {
                $schema = pathinfo($schema, PATHINFO_FILENAME);
            
                $className = "\\sprint\\app\\schemas\\{$schema}";
                
                $newSchema = new $className;
                
                $newSchema->upgrade();
            }
        }
    }
    
    public function schemaTable()
    {
        $schema = new \sprint\app\core\Schema();
        
        $schema->create($this->schemaTableName, function(BluePrintSchema $table)
        {
            $table->int("schema_id")->autoIncreament()->primaryKey();
            $table->varchar("schema_name")->notNull(true);
            $table->dateTime("schema_created_at", "CURRENT_TIMESTAMP")->notNull(false);
            
            $table->create();
        });
    }
}