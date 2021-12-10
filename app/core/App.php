<?php

namespace sprint\app\core;

use \sprint\Schema;
use \sprint\Entity;
use \sprint\app\exceptions\InvalidRouteException;
use \sprint\app\exceptions\FileNotFoundException;
use \sprint\app\exceptions\InvalidArgumentCounterException;
use \sprint\app\exceptions\PageNotFoundException;
use \sprint\app\exceptions\ForbiddenException;
use \sprint\app\exceptions\SecurityException;


class App {

    public $router;
    
    private $schema;
    
    private $entity;

    public static $app;
	
	private $error = array();

    
    /** Initialize main class as singleton object
     *
     * @param $dir is an constant that will be passed from the root file
     */
    public function __construct($dir)
    {
        self::$app = $this;
            
		if($_SERVER["SCHEMA_AUTO_RUN"] == "true") $this->schema = new Schema;     

		if($_SERVER["ENTITY_AUTO_RUN"] == "true") $this->entity = new Entity;

		$this->router = new Router($this, $dir);
    }
    
    /** Runs the executed route
     *
     */
    public function run()
    {   
        try
        {
            $this->router->resolve();
        } catch(InvalidRouteException $error)
        {
			$this->error = array(
				"codeError"      => $error->getCode(),
				"lineError"      => $error->getLine(),
				"fileError"       => $error->getFile(),
				"messageError"   => $error->getMessage()
			);
			
			$this->router->view("Error", $this->error);
        } catch(\InvalidArgumentCounterException $error)
        {
			$this->error = array(
				"codeError"      => $error->getCode(),
				"lineError"      => $error->getLine(),
				"fileError"       => $error->getFile(),
				"messageError"   => $error->getMessage()
			);
			
			$this->router->view("Error", $this->error);
        } catch(\FileNotFoundException $error)
        {
			$this->error = array(
				"codeError"      => $error->getCode(),
				"lineError"      => $error->getLine(),
				"fileError"       => $error->getFile(),
				"messageError"   => $error->getMessage()
			);

			$this->router->view("Error", $this->error);
        } catch(\Error $error)
        {
			$this->error = array(
				"codeError"      => $error->getCode(),
				"lineError"      => $error->getLine(),
				"fileError"       => $error->getFile(),
				"messageError"   => $error->getMessage()
			);
			
			$this->router->view("Error", $this->error);
        } catch(\PDOException $error)
        {
			$this->error = array(
				"codeError"      => $error->getCode(),
				"lineError"      => $error->getLine(),
				"fileError"       => $error->getFile(),
				"messageError"   => $error->getMessage()
			);
			
			try
			{
				$this->router->view("Error", $this->error);
			}catch(FileNotFoundException $error)
			{
				echo $error->getMessage();
			}
			
        }
    }

}
