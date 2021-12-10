<?php 

namespace sprint\app\core;

use \sprint\app\exceptions\{InvalidRouteException, FileNotFoundException};
use \sprint\app\helpers\{Pagination, Cart};

class Router
{
    protected $_controller  = 'Home';    
    protected $method       = 'index';    
    protected $params       = [];      
    protected $routers      = [];  
    
    private $currentRoute;    
    private $middlewares    = [];
    
    private $app;
    private $dir;
    private $controller;
    private $model;
    protected $response;
    protected $resquest;
    protected $session;
    protected $security;
    protected $cart;
    
    /** This is the main route function and needs to be public so we call outside the class
     *
	 * @example route("/", function(){ echo "This is an example"; });
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
     * pattern
	 * @return the instance of the Router class
     */
    public function __construct($app, $dir)
    {        
        $this->app = $app;       
        $this->dir = $dir;
        
        $this->controller   = new Controller;
        $this->model        = new Model;
        $this->response     = new Response;           
        $this->session      = new Session;        
        $this->security     = new Security;        
        $this->pagination   = new Pagination;
        $this->cart   		= new Cart;
    }
    
    /** This is the main route function and needs to be public so we call outside the class
     *
	 * @example route("/", function(){ echo "This is an example"; });
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
     * pattern
	 * @return the instance of the Router class
     */
    public function auto()
    {
        $url = explode("/", Request::getParseUrl());
        
        $url = $url ?? [$this->_controller, $this->method];

        if (isset($url[0]) && file_exists('app/controllers/' . ucfirst($url[0]) . '.php')) 
        {
            $this->_controller = ucfirst($url[0]);
            unset($url[0]);
        }else
        {
            return new InvalidRouteException("Invalid route informed.");
        }

        $className = "\\sprint\\app\\controllers\\{$this->_controller}";

        $this->_controller = new $className;

        if (isset($url[1])) 
        {
            if (method_exists($this->_controller, $url[1])) 
            {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        $this->params = $url ? array_values($url) : [];

        return call_user_func_array([$this->_controller, $this->method], $this->params);
    }
    
    /** This is the main route function and needs to be public so we call outside the class
     *
	 * @example route("/", function(){ echo "This is an example"; });
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
     * pattern
	 * @return the instance of the Router class
     */
    public function route($url, $callback)
    {    
        //the currentRoute variable is important so we can track which of the route was tagged by middleware
        $this->currentRoute = $url;
        
        //here we check if the request was get or post
        //the functionality is the same for both
        if(Request::isGet())
        {            
            $this->get($url, $callback);
            
        }else if(Request::isPost())
        {            
            $this->post($url, $callback);            
        }
        
        return $this;
    }
    
    /** The idea of this method is match in the uri passed by the user the parameters
     * This is achieved passing in the uri the name of the parameter in the bracketes, 
     * like this {param} 
     *
	 * @example http:localhost/sprint/example/{param}
	 * 
	 * @param string $pattern is the uri informed by user
	 * @return array of the matched params
     */
    private function patternParams($pattern) 
    {
        //just initialize the matches array as an empty value
        $matches = [];
        
        //we check if there is an match in the regular expression, checking {}
        if (preg_match_all('/{(\w+)}/', $pattern, $matches)) 
        {
            return $matches[1];
        }
        
        return $matches;
    }

    /** This will replace all forward slashes into collon
     *
	 * @example example/{param} will be like this, example:{param}
	 * 
	 * @param string $pattern is the uri informed by user
	 * @return the replaced string
     */
    private function withEscapedSlashes($pattern) 
    {
        return str_replace('/', ':', $pattern);
    }

    /** This is the main route function and implements both get or post method depending on the request made
     *
	 * @example route("/", function(){ echo "This is an example"; });
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
     * pattern
	 * @return the instance of the Router class
     */
    private function withParams($pattern) 
    {        
        return preg_replace('/{\w+}/', '([:\w\-?=?]*)', $pattern);
    }
    
    /** To call the route as get method
     *
	 * @example get("/", function(){ echo "This is an example"; });
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
     * pattern
	 * @return the instance of the Router class
     */
    public function get($url, $callback)
    {
        $this->routers["get"][$url] = $callback;
        
        return $this;
    } 
    
    /** To call the route as post method
     *
	 * @example post("/", function(){ echo "This is an example"; });
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
     * pattern
	 * @return the instance of the Router class
     */
    public function post($url, $callback)
    {
        $this->routers["post"][$url] = $callback;
        
        return $this;
    } 
    
    /** this method will vaildate the if the request uri has valid route and execute
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
     * pattern
	 * @return callable user predefined function
     */
    public function resolve()
    {
        $url   = Request::getParseUrl() ?? "/";	
        
        foreach($this->routers as $handler)
        {
            foreach($handler as $uri => $action)
            {
                $pattern = $this->withParams($uri);
                
                $pattern = $this->withEscapedSlashes($pattern);
                
                if(preg_match("/^{$pattern}$/i", $this->withEscapedSlashes($url)))
                {
                    $this->applyMiddlewares($url);
                    
                    $callback   = $this->routers[Request::getMethod()][$uri];
                    
                    if (preg_match_all("/^{$pattern}$/i", $this->withEscapedSlashes($url), $matches)) 
                    {                        
                        $patternParams = $this->patternParams($uri);
                        
                        if(!empty($patternParams))
                        {
                            foreach($matches as $key => $value)
                            {
                                if($key != 0)
                                {
                                    $this->params[] = $value[0];
                                }
                            }
                        }                    
                    }
                    
                    if(is_array($callback))
                    {       
                        if (file_exists('app/controllers/' . ucfirst($callback[0]) . '.php')) 
                        {
                            $className = "\\sprint\\app\\controllers\\{$callback[0]}";

                            $callback[0] = new $className;

                            if (!method_exists($callback[0], $callback[1])) 
                            {
                                $callback[1] = $this->method;
                            }
                        }

                    }else if(is_string($callback) && strlen($callback) > 0)
                    {
                        $callback = explode(":", $callback);
                        
                        $callback[0] = ucfirst($callback[0]);
                            
                        $className = "\\sprint\\app\\controllers\\{$callback[0]}";

                        $callback[0] = new $className;

                        if (!method_exists($callback[0], $callback[1])) 
                        {
                            $callback[1] = $this->method;
                        }
                    }
                    
                    try
                    {
                        return call_user_func_array($callback, array_values($this->params));                        
                    }catch(\Exception $error)
                    {
                        echo $error->getMessage();
                        
                        exit();
                    }
                }
                  
            }
        }
        
        throw new InvalidRouteException("Invalid route informed.");        
        exit();
    }   
    
    /** This is the main route function and needs to be public so we call outside the class
     *
	 * @example route("/", function(){ echo "This is an example"; });
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
     * pattern
	 * @return the instance of the Router class
     */
    public function middleware(array $middlewares = [], $routes = null, array $notAllowedRoutes = [])
    {
        if(!empty($middlewares))
        {                        
            $middleware[0] = ucfirst($middlewares[0]);

            $className = "\\sprint\\app\\controllers\\{$middleware[0]}";

            $middleware[0] = new $className;
            
            $middleware[1] = $middlewares[1];
            
            //will apply the middleware for grouped routes
            if(is_array($routes) && !empty($routes))
            {
                foreach($routes as $value)
                {
                    $this->middlewares[] = array(
                        "route"             => $value,
                        "controller"        => $middleware[0],
                        "action"            => $middleware[1],
                        "notAllowedRoutes"  => []
                    );
                } 
                
                return $this;               
            }//will apply the middleware for all routes
            else if(is_string($routes) && strlen($routes))
            {
                $this->middlewares[] = array(
                    "route"             => "*",
                    "controller"        => $middleware[0],
                    "action"            => $middleware[1],
                    "notAllowedRoutes"  => $notAllowedRoutes
                );
                
                return $this;
            }
            
            //will apply the middleware in each route
            $this->middlewares[] = array(
                "route"             => $this->currentRoute,
                "controller"        => $middleware[0],
                "action"            => $middleware[1],
                "notAllowedRoutes"  => []
            );
            
            return $this;
        }
    }
    
    /** this function will apply the middleware to the routes, will check all middlewares informed and verify if is allowed to execute to certain route or not 
     *
	 * @example $this->applyMiddlewares($uri);
	 * 
	 * @param string   $route is the uri informed by user
	 * @return will execute callback for the specified middleware [controller, action]
     */
    private function applyMiddlewares(String $route)
    {
        if(!empty($this->middlewares))
        {
            foreach($this->middlewares as $value)
            {
                if($value["route"] == $route || $value["route"] == "*")
                {
                    if(in_array($route, $value["notAllowedRoutes"]))
                    {
                        continue;
                    }                    
                    call_user_func([$value["controller"], $value["action"]]);
                }
            }
        }
    }
    
    /** this method is an override of the view method in the controller class
     *
	 * @example $this->view("Welcome", $data);
	 * 
	 * @param string $view is the view informed by user
	 * @param array $data an associative array, represents the data passed to the view via 
     * controller
	 * @return view
     */
    public function view(String $view, array $data = [])
    {
        return $this->controller->view($view, $data);
    }
    
    /** This is the main route function and needs to be public so we call outside the class
     *
	 * @example route("/", function(){ echo "This is an example"; });
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
     * pattern
	 * @return the instance of the Router class
     */
    public function model(String $model)
    {
        return $this->controller->model($model);
    }
    
    /** This is the main route function and needs to be public so we call outside the class
     *
	 * @example route("/", function(){ echo "This is an example"; });
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
     * pattern
	 * @return the instance of the Router class
     */
    public function query(String $name)
    {
        return Request::query($name);
    }
    
    /** This is the main route function and needs to be public so we call outside the class
     *
	 * @example route("/", function(){ echo "This is an example"; });
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
        * pattern
	 * @return the instance of the Router class
     */
    public function getBody()
    {
        return Request::getBody();
    }
    
    /** This is the main route function and needs to be public so we call outside the class
     *
	 * @example route("/", function(){ echo "This is an example"; });
	 * 
	 * @param string   $url is the uri informed by user
	 * @param callable $callback is function to be executed if the uri matches the url 
     * pattern
	 * @return the instance of the Router class
     */
    public function register( array $routes )
    {
        $app = $this->app;
        
        foreach($routes as $route)
        {
            $routePath = $this->dir."/app/routes/{$route}.php";

            if(file_exists($routePath))
            {
                require_once ($routePath);
            }            
        }
    }
    
}