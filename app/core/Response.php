<?php 
namespace sprint\app\core;

use \sprint\app\helpers\Helpers;

class Response
{    
    public function setResponseCode(int $code)
	{
		http_response_code($code);
	}
    
    public function responseType(String $type = "")
    {
        switch ($type) 
        {
            case 'xml':
                header('Content-type: text/xml; charset=UTF-8');
                break;
            case 'json':
                header("Content-Type: application/json; charset=UTF-8");
                break;
            default:
                header("Content-Type: text/html; charset=UTF-8");
        }
    }
    
    public function redirect(String $url = "", int $code = 301)
    {
        header("Location: ". Helpers::root() . $url, true, $code);        
        exit;
    }
    
}