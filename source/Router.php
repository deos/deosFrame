<?php 

namespace Core;

/**
 * Router class for getting the current route, creating the controller and running it
 * 
 * @package deosFrame
 * @author deos
 */
final class Router {

	/**
	 * Current url path
	 * 
	 * @var string
	 */
	private $path;
	
	/**
	 * Current path parameters
	 * 
	 * @var array(string)
	 */
	private $params = array();
	
	/**
	 * Current controller
	 * 
	 * @var Core\Controller
	 */
	private $controller;
	
	/**
	 * System path routes
	 * 
	 * @var array(array(mixed))
	 */
	private $routes;
	
	/**
	 * Current route name
	 * 
	 * @var string
	 */
	private $currentRoute = 'default';
	
	/**
	 * Default route pattern
	 * 
	 * @var string
	 */
	private $defaultPattern = '/:module/:controller/:action/';
	
	/**
	 * Default path parameters
	 * 
	 * @var array(string)
	 */
	private $defaults = array(
		'prefix' => '',				//for folder prefixes
		'module' => 'index',		//module name
		'controller' => 'index',	//controller name
		'action'  => 'index',		//action name
		'format' => 'html'			//output format
	);
	
	/**
	 * Class constructor
	 * 
	 * @param array $routes System path routes
	 * 
	 * @return void
	 */
	public function __construct(array $routes = array()){
		//get current url		
		$path = parse_url(preg_replace('/^(.*?)'.str_replace('/', '\/', URL_PREFIX).'/', '', $_SERVER['REQUEST_URI']));
		$path = $path['path'];

		//lets get the format extension, default to html
		$info = pathinfo($path);
		if(isset($info['extension'])){
			$this->params['format'] = (in_array($info['extension'], array('html', 'htm', 'php')) ? 'html' : $info['extension']);
			$path = substr($path, 0, -1*strlen($info['extension'])-1);
		}
		else{
			$this->params['format'] = 'html';
		}
		
		//set default route if it is not there
		if(!array_key_exists('default', $routes)){
			$routes['default'] = array(
				'pattern' => $this->defaultPattern
			);
		}
		
		$this->path = $path;
		$this->routes = $routes;
	}
	
	/**
	 * Start router
	 * 
	 * @return string
	 */
	public function start(){
		$this->findRoute();
	
		$this->getParams();
				
		return $this->runController();
	}
	
	/**
	 * Find current route
	 * 
	 * @return array(array(mixed))
	 */
	private function findRoute(){
		$pathParts = explode('/', $this->path);

		foreach($this->routes as $routeName=>$route){
			$patternParts = explode('/', $route['pattern']);

			//check the route
			foreach($patternParts as $i=>$part){
				if(!$part){
					continue;
				}
				
				if($part[0]==':'){
					//the 3 main parts dont need to be there, but everything else needs to be
					if($this->isDefaultPart(substr($part, 1))){
						continue;
					}
					elseif(!isset($pathParts[$i]) OR !$pathParts[$i]){
						continue 2;
					}
				}
				elseif(!isset($pathParts[$i]) OR $part != $pathParts[$i]){
					continue 2;
				}
			}
			
			$this->currentRoute = $routeName;
			return $route;
		}
		
		echo 'Error: Route not found!';
		exit;
	}
	
	/**
	 * Get current parameters from current url matching current path
	 * 
	 * @return void
	 */
	private function getParams(){
		$pathParts = explode('/', $this->path);
		$patternParts = explode('/', $this->routes[$this->currentRoute]['pattern']);
		
		//lets start with the defaults
		$this->params += $this->defaults;
		
		//route defaults are next
		if(array_key_exists('defaults', $this->routes[$this->currentRoute])){
			$this->params = $this->routes[$this->currentRoute]['defaults'] + $this->params;
		}
		
		//get the information from the path
		foreach($patternParts as $i=>$part){
			if(!$part OR $part[0]!=':'){
				continue;
			}

			$part = substr($part, 1);
			$pathParts[$i] = (isset($pathParts[$i]) ? urldecode($pathParts[$i]) : null);
			$pathPart = ($pathParts[$i] ? $pathParts[$i] : $this->defaults[$part]);

			if(($params = strstr($pathPart, '?'))){
				$pathPart = str_replace($params, '', $pathPart);
			}

			$this->params[$part] = $pathPart;
		}
	}
	
	/**
	 * Get controller, check current action and run it
	 * 
	 * @return string
	 */
	private function runController(){ 
		$controllerName = 'Controller\\'.($this->params['prefix'] ? $this->params['prefix'].'\\' : '').ucfirst($this->params['module']).'\\'.ucfirst($this->params['controller']).'Controller';
		
		try{
			/*
			if(!class_exists($controllerName)){
				throw new Exception('Controller not found!');	
			}
			*/
			
			$template = new View('/template/template.html.php');
			
			$this->controller = new $controllerName($this->params['action'], $this->params['format'], $template);
			
			Registry::set('controller', $this->controller);
			
			//check if the class is a \Core\Controller
			if(!$this->controller instanceof Controller){
				throw new Exception('Controller class not a instance of \Core\Controller!');	
			}
			
			$checkForActions = array($this->params['action'].ucfirst($this->params['format']).'Action', $this->params['action'].'Action');
			$toCall = null;
			
			foreach($checkForActions as $check){
				if(is_callable(array($this->controller, $check))){
					$toCall = $check;
					break;
				}
			}
			
			if(!$toCall){
				throw new Exception('Action not found!');
			}

			$this->controller->$toCall();

			return $this->controller->render();
		}
		catch(\Exception $e){
			echo 'Error: '.$e->getMessage();
			exit;
		}
	}
	
	/**
	 * Helper function to check if part is default part 
	 * 
	 * @param string $part Part name
	 * 
	 * @return boolean
	 */
	private function isDefaultPart($part){
		return in_array($part, array('module', 'controller', 'action'));
	}
	
	/**
	 * Returns the current controller
	 * 
	 * @return Core\Controller
	 */
	public function getController(){
		return $this->controller;
	}
	
	/**
	 * Get a param of the current route
	 * 
	 * @param string $param Parameter name
	 * 
	 * @return mixed
	 */
	public function get($param){
		return (isset($this->params[$param]) ? $this->params[$param] : null);
	}
	
	/**
	 * Get current route name
	 * 
	 * @return string
	 */
	public function getCurrentRoute(){
		return $this->currentRoute;
	}
	
	/**
	 * Create a url
	 * 
	 * @param array   $params Parameter array
	 * @param string  $route  Route name
	 * @param boolean $reset  If true, ignores current params and starts with default params
	 * 
	 * @return string
	 */
	public function url(array $params = null, $route = null, $reset = null){
		if(!$route){
			$route = $this->currentRoute;
		}
		elseif($route!=$this->currentRoute){
			//set reset to true if route changes and reset is not set itself
			$reset = ($reset!==null ? true : $reset);
		}
		
		if(!isset($this->routes[$route])){
			throw new Exception('There is no route '.$route);
		}
		
		if($params === null){
			$params = array();
		}
		
		if($reset){
			$params = array_merge($this->defaults, $params);
		}
		else{
			$params = array_merge($this->defaults, $this->params, $params);
		}

		$patternParts = explode('/', $this->routes[$route]['pattern']);
		$path = array();
		$dontIgnore = false;
		
		for($i=count($patternParts)-1; $i>=0; $i--){		
			$part = $patternParts[$i];
			
			if(!$part){
				continue;
			}
			
			if($part[0]!=':'){
				$path[] = urlencode($part);
				$dontIgnore = true;
				continue;
			}
			
			$part = substr($part, 1);
			$param = (isset($params[$part]) ? $params[$part] : null);
			
			//the 3 main parts dont need to be there, but everything else needs to be
			if(!$param AND !$this->isDefaultPart($part)){
				throw new Exception('Missing parameter for url: '.$part);
			}
			
			if($param != 'index' OR $dontIgnore){
				$path[] = urlencode($param);
				$dontIgnore = true;
			}
			
			unset($params[$part]);
		}
		
		$url = URL_PREFIX.'/'.implode('/', array_reverse($path)).(count($path)==0 ? 'index' : '').'.'.$params['format'];
		
		//this preserved params dont go in the url
		unset($params['module'], $params['controller'], $params['action'], $params['format'], $params['prefix']);
		
		if(count($params)>0){			
			$url .= '?'.http_build_query($params);
		}
		
		return $url;
	}
	
	/**
	 * Do a header redirect
	 *  
	 * @param string $url Url to riderect to
	 * 
	 * @return void
	 */
	public function redirect($url){
		$host  = $_SERVER['HTTP_HOST'];

		header('Location: http://'.$host.$url);
		exit;
	}
	
}

?>
