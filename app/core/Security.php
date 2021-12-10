<?php

namespace sprint\app\core;

use \sprint\app\exceptions\SecurityException;

class Security
{
    private $session;
    
    public function __construct()
    {
        $this->session = new Session;
    }
    
    // Generate a token for use with CSRF protection.
    // Does not store the token.
    private function csrfToken() 
    {
        return md5(uniqid(rand(), TRUE));
    }

    // Generate and store CSRF token in user session.
    public function createCsrfToken() 
    {        
        $token = $this->csrfToken();
        
        $this->session->set('csrfToken', $token);
        
        $this->session->set('csrfTokenTime', time());
        
        return $token;
    }

    // Destroys a token by removing it from the session.
    public function destroyCsrfToken() 
    {
        $this->session->unset(["csrfToken", "csrfTokenTime"]);
        
        return true;
    }

    // Return an HTML tag including the CSRF token 
    // for use in a form.
    // Usage: echo $this->createCsrfToken();
    public function csrfTokenTag() 
    {
        $token = $this->createCsrfToken();
        
        return "<input type=\"hidden\" name=\"csrfToken\" value=\"".$token."\">";
    }

    // Returns true if user-submitted POST token is
    // identical to the previously stored SESSION token.
    // Returns false otherwise.
    public function isValidCsrfToken() 
    {
        $post = Request::getBody();
        
        if(isset($post['csrfToken'])) 
        {
            $userToken      = $post["csrfToken"];
            
            $storedToken    = $this->session->get("csrfToken");
            
            return $userToken === $storedToken;
        } else 
        {
            return false;
        }
    }

    // You can simply check the token validity and 
    // handle the failure yourself, or you can use 
    // this "stop-everything-on-failure" function. 
    public function invalidCsrfToken() 
    {
        if(!$this->isValidCsrfToken()) 
        {
            return new securityException("You provided invalid token.");
        }
    }

    // Optional check to see if token is also recent
    public function isRecentCsrfToken() 
    {
        $maxElapsed = 60 * 60 * 24; // 1 day
        
        $storedTime = $this->session->get("csrfTokenTime");
        
        if(!empty($storedTime)) 
        {
            return ($storedTime + $maxElapsed) >= time();
        } else 
        {
            // Remove expired token
            $this->destroyCsrfToken();
        
            return false;
        }
    }

    // check if the request was done with same domain
    public function requestIsSameDomain() 
    {
        if(empty(Request::refer())) 
        {
            // No refererer sent, so can't be same domain
            return new securityException("No refererer sent, so can't be same domain.");
        } else 
        {
            // is same domain returns true or thrown an exception
            return (Request::referHost() == Request::serverHost()) ? true : new securityException("No refererer sent, so can't be same domain.");
        }
    }

}