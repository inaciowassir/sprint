<?php 
ob_start();

session_start();

require_once __DIR__.'/vendor/autoload.php';

use \sprint\app\core\App;

$dotenv = Dotenv\Dotenv::CreateImmutable(__DIR__);

$dotenv->load();

try
{
    $app	= new App(__DIR__);
    
    $app->router->register(
    [
        "WelcomeRoute",
    ]);

    $app->run();
    
}catch(\Error $error)
{
    $data = array(
        "code"      => $error->getCode(),
        "line"      => $error->getLine(),
        "fie"       => $error->getFile(),
        "message"   => $error->getMessage()
    );
    
    $app->router->view("Error", $data);
}

ob_end_flush();