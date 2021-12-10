<?php

namespace sprint\app\helpers;

use \sprint\app\core\Model;

class Pagination
{
    private $anchors            = [];
    private $lists              = [];
    
    public $addUrl;
	public $firstAndLast        = ["First Page","Last Page"];
	public $asUl                = true;
    public $total;
    public $links               = 4;
    public $limit               = 12;
    public $offset              = 0;
    public $page                = 1;
    public $url;
    public $classes             = array(
                                    "a" => array(
                                        "style" => ["page-link"],
                                        "active"=> ["active"],
                                    ),
                                    "li" => array(
                                        "style" => ["page-item"],
                                    ),
                                    "ul" => array(
                                        "style" => ["pagination"],
                                    ),
                                    "div" => array(
                                        "style" => [],
                                    ),
                                );
    
    public function offset()
    {
        $this->offset = ($this->limit * $this->page) - $this->limit;
    }
    
    public function page()
    {
        $style = $this->getClassValue("a", "style");
        
        $active = $this->getClassValue("a", "active");
		
		$active = array_merge($style["style"], $active["active"]);
		
        if ($this->total > $this->limit):
        
            $totalPages = ceil($this->total / $this->limit);
		
			$this->a(1, $this->firstAndLast[0] , $style["style"]);
        
            for ($i = $this->page - $this->links; $i <= $this->page - 1; $i++):
                if ($i >= 1):
                    $this->a($i, $i, $style["style"]);
                endif;
            endfor;
        
            $this->a($this->page, $this->page, $active);
        
            for ($i = $this->page + 1; $i <= $this->page + $this->links; $i++):
                if ($i <= $totalPages):
                    $this->a($i, $i, $style["style"]);
                endif;
            endfor;
		
			$this->a($totalPages, $this->firstAndLast[1] , $style["style"]);
            
            if($this->asUl === true)
            {
                $this->li();
                return $this->ul();
            }else
            {
                return $this->div();
            }
        
        endif;        
    }
    
    private function a($page, $content, array $classes = [])
    {
        $url = !empty($this->url) ? rtrim($this->url, "/") ."/" : "?page=";
        
        $this->anchors[] = sprintf('<a href="' . Helpers::root() . $url. '%d" class="%s">%s</a>', $page, implode(" ", $classes), $content);
        
        return $this;
    }
    
    private function li()
    {
        $class = $this->getClassValue("li", "style");
        
        foreach($this->anchors as $anchor)
        {
            $this->lists[] = sprintf('<li class="%s">%s</li>', implode(" ", $class["style"]), $anchor);
        }        
    }
    
    private function ul()
    {
        $class = $this->getClassValue("ul", "style");
        
        $lists = implode("\n", $this->lists);  
		
        return sprintf('<ul class="%s">%s</ul>', implode(" ", $class["style"]), $lists);
    }
    
    private function div()
    {
        $class = $this->getClassValue("div", "style");
        
        $anchors = implode("\n", $this->anchors);  
		
        return sprintf('<div class="%s">%s</div>', implode(" ", $class["style"]), $anchors);
    }
                       
   private function getClassValue($key, $key1)
   {
        return array_key_exists($key, $this->classes) ? 
        (
            array_key_exists($key1, $this->classes[$key])
            ? $this->classes[$key] : []
        ) : [];
   }
}