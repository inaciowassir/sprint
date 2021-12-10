<?php 

namespace sprint\app\core;

use \sprint\app\helpers\GetterSetterGenerator;

class BluePrintEntity
{
    private  $property           = "";    
    private  $entity             = "";    
    private  $propertyToAssign   = [];    
    private  $column             = [];    
    
    public function __construct($entity)
    {
        $this->entity = $entity;
    }
    
    public function setAsProtected(String $property, $default = null)
    {
        $this->propertyToAssign = $property;
        
        $default = $default === null ? "" : " = ".$this->assignValues($default);
        
        $this->property .= "\tprotected $".$property.$default.";\n";
        
        return $this;
    }
    
    public function setAsPrivate(String $property, $default = null)
    {
        $this->propertyToAssign = $property;
        
        $default = $default === null ? "" : " = ".$this->assignValues($default);
        
        $this->property .= "\tprivate $".$property.$default.";\n";
        
        return $this;
    }
    
    private function assignValues($value)
    {
        if(is_string($value))
        {
            return "\"{$value}\"";
        }

        return $value;
    }
    
    public function db($column)
    {        
        $this->column[] = array(
            "property"  => $this->propertyToAssign,
            "value"     => $column
        );
    }
    
    public function create()
    {
        $construct = "\tpublic function __construct(array \$result = []){\n";
        
        foreach($this->column as $value)
        {
            $construct .= "\t\t\$this->".$value["property"]." = \$result[\"".$value["property"]."\"];\n";
        }
        
        $construct .= "\t}";
        
        $this->property = "class ".$this->entity."\n{\n".$this->property."\n\n{$construct}\n\n{{content}}\n\n}";
        
        $generator = new GetterSetterGenerator($this->property);
        
        $generated = $generator->generate();
        
        $entityFile = str_replace("{{content}}", $generated, $this->property);
        
        $entityPath = "app/entities/entity";
        
        if(!file_exists($entityPath))
        {
            mkdir($entityPath, 0755);
        }
        
        $file = $entityPath . "/". ucfirst($this->entity) . ".php";
        
        file_put_contents($file, "<?php \n\nnamespace sprint\\entities\\entity; \n\n".$entityFile);
    }    
}