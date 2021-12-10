<?php
namespace sprint\app\core;

class Session
{
    //this key will be used
    const SESSION_KEY = "SPRINT_SESSION_KEY"; 
    
    public function id()
    {
        // Regenerate session ID to invalidate the old one.
        // Super important to prevent session hijacking/fixation.
        return session_regenerate_id();
    }
    
    public function setUserIpAgent($key)
    {
        // Regenerate session ID to invalidate the old one.
        // Super important to prevent session hijacking/fixation.
        $this->id(); 
        
        //those values a important to keep track of the user to prevent session hijacking
        $this->set($key, array(
            "ip"    => Request::ip(),
            "agent" => Request::agent()
        ));
    }
    
    public function set($key, $value, $multi = false)
    {
		if($multi === false)
		{			
        	//here we set session with the specified key and value
	        $_SESSION[self::SESSION_KEY][$key] = $value;
		}else
		{
			$_SESSION[self::SESSION_KEY][$key][] = $value;
		}
    }
    
    public function replace($key, $index, $attribute, $value)
    {
        //here we set session with the specified key and value
        $_SESSION[self::SESSION_KEY][$key][$index][$attribute] = $value;
    }
    
    public function get($key)
    {
        //here we get the session value from the specified key
        return $_SESSION[self::SESSION_KEY][$key] ?? null;
    }
	
    public function flash($key)
    {
        //the idea of flash is to unset the value of the session after being displayed at the first time
        //to achieve this point we store in variable the value of the session for the informed key
        $session = $this->get($key) ?? null;
        
        //here we unset the session in that informed key
        $this->remove($key);
        
        //here we return the value of the session stored in the variable
        return $session;
    }
    
    public function remove($key, $index = null)
    {
        if(is_string($key))
        {
			if($index !== null)
		   	{
				unset($_SESSION[self::SESSION_KEY][$key][$index]);
		   	}else
			{
				unset($_SESSION[self::SESSION_KEY][$key]);
		   	}
            	
        }else if(is_array($key))
        {
            foreach($key as $indexes)
            {
				if($index !== null)
				{
					unset($_SESSION[self::SESSION_KEY][$indexes][$index]);
				}else
				{
					unset($_SESSION[self::SESSION_KEY][$indexes]);
				}
				
            }
        }
    }
}