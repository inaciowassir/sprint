<?php

namespace sprint\app\core;


//cookie->set("inacio", 20)->d(20);
class Cookie
{
    public function set($name, $value, $expire, $path = null, $domain = null, $secure = false, $httponly = true)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
        
        return $this;
    }
    
    private function s($seconds)
    {
        
        return time() + $seconds;
    }
    
    private function h($hours)
    {
        
        return time() + (60 * 60 * $hours);
    }
    
    private function d($days)
    {
        
        return time() + (60 * 60 * 24 * $days);
    }
}