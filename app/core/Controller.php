<?php

namespace sprint\app\core;

/** this is the base core controller which all controllers will inherit from
 * 
 * Examples: In the controllers classes
 * 
 * 1. class Welcome extends \sprint\app\core\Controller
      {
 
            public function renderViewAndPassModelData()
            {
                $model = $this->model("Welcome");
                
                $data["welcomeData"] = $model->getWelcomeData();
                
                $this->view("Welcome", $data);
            }
      }	

 * 
 * 
 * 2. use \sprint\app\core\Controller;
 
    class Welcome extends Controller
    {
        private $data;
        private $model;
        
        public function __construct()
        {
            $this->model = $this->model("Welcome");
        }
        
        public function renderViewAndPassModelData()
        {
            $this->data["welcomeData"] = $this->model->getWelcomeData();

            $this->view("Welcome", $this->data);
        }
    }

 *   
 * 
 */

use \sprint\app\helpers\Helpers;
use \sprint\app\helpers\TemplateEngine as Template;
use \sprint\app\exceptions\FileNotFoundException;

class Controller
{    
    public $viewPath    = null;
    
    /** this method will create an instance of the model informed by user
     *
	 * @example $this->model("Welcome");
	 * 
	 * @param string $model is the model informed by user
	 * @return an instance of the model
     */
    public function model(String $model) 
    {
        //checking if the informed model exists in models path
        if (file_exists('app/models/' . $model . '.php')) 
        {
            $className = "\\sprint\\app\\models\\{$model}";
            return new $className;
        }
    }

    /** this method will call the view informed by user
     *
	 * @example $this->view("Welcome", $data);
	 * 
	 * @param string $view is the view informed by user
	 * @param array $data an associative array, represents the data passed to the view via 
     * controller
	 * @echo view informed as buffered data
     */
    public function view(String $view, array $resources = [], String $type = "") 
    {
        try 
		{
			echo $this->compileTemplate($view, $resources, $type);
		} catch (FileNotFoundException $error) 
		{
			//Catch Statement
			echo $error->getMessage();
		}
    }
    
    /** this method will compile the template to the php code
     *
	 * @example echo $this->compileTemplate("Welcome", $data);
	 * 
	 * @param string $view is the view informed by user
	 * @param array $resources an associative array, represents the data passed to the view via controller
     * @param string $type response type (html, json or xml)
	 * @return view informed
     */
    public function compileTemplate(String $view, array $resources = [], String $type = "") 
    {
        //will start output buffer
        ob_start();
        
        $response = new Response;
        
        $file = Helpers::viewPath($this->viewPath) .  $view . '.php';
        
        //checking if the informed view exists in views path
        if (file_exists($_SERVER["VIEWS_FOLDER"] . $file)) 
        {			
            $resources["root"]   = Helpers::root();
            $resources["asset"]  = Helpers::asset();
            $resources["upload"] = Helpers::upload();
            
            //calls the method view in the TemplateEngine class
            Template::view($file, $resources);
        }else
        {
            //if the file does not exists throw an FileNotFoundException
            throw new FileNotFoundException("we are unable to locate the file in your views.");
        }
        
        //this will set the http_response type to either (html, json or xml)
        $response->responseType($type);
        
        //will output and clean buffer
        return ob_get_clean();
        
        exit();
    }
}
