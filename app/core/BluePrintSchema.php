<?php

namespace sprint\app\core;

class BluePrintSchema extends Model
{
    public $schema;
    public $table;
    private $nextLine = false;
    
    public function setTable(String $table)
    {
        $this->table = $table;
    }
    
    public function autoIncreament()
    {
        $this->schema .= " AUTO_INCREMENT";
        
        return $this;
    }
    
    public function primaryKey()
    {
        $this->schema .= " PRIMARY KEY";
        
        return $this;
    }
    
    public function int(String $column, int $length = 11, int $default = 0)
    {
        $default = $default === 0 ? "" : " DEFAULT ".intval($default);
        
        $length = $length > 11 ? 11 : $length;
        
        $comma = "";
        
        if($this->nextLine == true)
        {
            $comma = ",";
        }
        
        $this->schema .= "{$comma}`{$column}` INT({$length}){$default}";
        
        $this->nextLine = true;
        
        return $this;
    }
    
    public function decimal(String $column, array $length = [10,2], int $default = 0)
    {
        $default = $default === 0 ? "" : " DEFAULT ".$default;
        
        $length     = (isset($length[0])) ? intval($length[0]) : 10;
        $decimal    = (isset($length[1])) ? intval($length[1]) : 2;
        
        $comma = "";
        
        if($this->nextLine == true)
        {
            $comma = ",";
        }
        
        $this->schema .= "{$comma}`{$column}` DECIMAL({$length},{$decimal}){$default}";
        
        $this->nextLine = true;
        
        return $this;
    }
    
    public function varchar(String $column, int $length = 255, $default = NULL)
    {
        $default = $default === NULL ? "" : " DEFAULT ".$this->defaultValues($default);
        
        $length = $length > 255 ? 255 : $length;
        
        $comma = "";
        
        if($this->nextLine == true)
        {
            $comma = ",";
        }
        
        $this->schema .= "{$comma}`{$column}` VARCHAR({$length}){$default}";
        
        $this->nextLine = true;
        
        return $this;
    }
    
    public function text(String $column, int $length = 255, $default = NULL)
    {
        $default = $default === NULL ? "" : " DEFAULT ".$this->defaultValues($default);
        
        $comma = "";
        
        if($this->nextLine == true)
        {
            $comma = ",";
        }
        
        $this->schema .= "{$comma}`{$column}` TEXT{$default}";
        
        $this->nextLine = true;
        
        return $this;
    }
    
    public function enum(String $column, $default)
    {
        $default = $default === NULL ? "" : $this->defaultValues($default);
        
        $comma = "";
        
        if($this->nextLine == true)
        {
            $comma = ",";
        }
        
        $this->schema .= "{$comma}`{$column}` ENUM({$default})";
        
        $this->nextLine = true;
        
        return $this;
    }
    
    public function date(String $column, $default = NULL)
    {
        $default = $default === NULL ? "" : " DEFAULT ".$default;
        
        $comma = "";
        
        if($this->nextLine == true)
        {
            $comma = ",";
        }
        
        $this->schema .= "{$comma}`{$column}` DATE{$default}";
        
        $this->nextLine = true;
        
        return $this;
    }
    
    public function dateTime(String $column, $default = NULL)
    {
        $default = $default === NULL ? "" : " DEFAULT ".$default;
        
        $comma = "";
        
        if($this->nextLine == true)
        {
            $comma = ",";
        }
        
        $this->schema .= "{$comma}`{$column}` DATETIME{$default}";
        
        $this->nextLine = true;
        
        return $this;
    }
    
    public function timeStamp(String $column, $default = NULL)
    {
        $default = $default === NULL ? "" : " DEFAULT ".$default;
        
        $comma = "";
        
        if($this->nextLine == true)
        {
            $comma = ",";
        }
        
        $this->schema .= "{$comma}`{$column}` TIMESTAMP{$default}";
        
        $this->nextLine = true;
        
        return $this;
    }
    
    public function notNull($flag = true)
    {
        $this->schema .= ($flag === false) ? " NULL" : " NOT NULL";
        
        return $this;
    }
    
    public function alterTable()
    {
        $this->schema .= $this->alter($this->table);
    }
    
    public function addColumn(String $column)
    {
        $this->schema .= $this->add_column($column);
    }
    
    public function dropColumn(String $column)
    {
        $this->schema .= $this->drop_column($column);
    }
    
    public function renameTable(String $newTableName)
    {
        $this->schema .= $this->rename_to($newTableName);
    }
    
    private function defaultValues($value)
    {
        $default = "";
        
        if(is_string($value) || is_int($value))
        {
            if(is_int($value))
            {
                $default = $value;
            }else
            {
                $default = "'{$value}'";
            }
        }else if(is_array($value))
        {
            $columns = array_map(
            function($v)
            { 
                return "'{$v}'"; 
                
            }, $value);
            
            $default = implode(", ", $columns);
        }
        
        return $default;
    }
    
    public function create()
    {
        $this->schema = "CREATE TABLE IF NOT EXISTS `{$this->table}`({$this->schema})Engine=InnoDB DEFAULT charset=UTF8;";
        
        return $this->save();
    }
    
    public function save()
    {        
        $this->query = $this->schema;
        $this->run();
    }
    
    public function show()
    {
        echo $this->schema;
    }
}