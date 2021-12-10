<?php

namespace sprint\app\controllers;

class Website extends \sprint\app\core\Controller
{
    public function index()
    {
        $this->view("Website");
    }
    
}