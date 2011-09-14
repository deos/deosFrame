<?php 

namespace Core;

/**
 * View class, creates a object of a specific view and renders it on demand
 * 
 * @package deosFrame
 * @author thomas
 */
class View {

	/**
	 * Path to corresponding php html/json/... template file
	 * 
	 * @var string
	 */
	private $path;
	
	/**
	 * Variables on the view
	 * 
	 * @var array(mixed)
	 */
	private $vars = array();
	
	/**
	 * If true, the view gets the vars of the main view before rendering (without overwriting!)
	 * @var unknown_type
	 */
	private $getMainViewVars = false;
	
	/**
	 * Class constructor
	 * 
	 * @param string  $path            Path to the corresponding php html/json/... template file, beginning from APP_PATH
	 * @param boolean $getMainViewVars If true, the view gets the vars of the main view before rendering (without overwriting!) 
	 */
	public final function __construct($path, $getMainViewVars = false){
		$this->path = APP_PATH.$path;
		$this->getMainViewVars = $getMainViewVars;
	}

	/**
	 * Renders the view and returns the html as 
	 * 
	 * @return string
	 */
	public final function render(){
		if(!file_exists($this->path)){
			throw new Exception('Could not load View '.$this->path.'!!');
		}
		
		if($this->getMainViewVars){
			$this->setVars(Registry::get('controller')->getView()->getVars(), true);
		}
		
		ob_start();
		require($this->path);
		return ob_get_clean();
	}
	
	/**
	 * Get all variables of this view
	 * 
	 * @return array(mixed)
	 */
	public final function getVars(){
		return $this->vars;
	}
	
	/**
	 * Set several variables of this view
	 * 
	 * @param array   $vars               The variables to set to the view
	 * @param boolean $disableOverwriting If true, nothing will get overwritten
	 * 
	 * @return this
	 */
	public final function setVars(array $vars, $disableOverwriting = false){
		if($disableOverwriting){
			$this->vars += $vars;
		}
		else{
			$this->vars = $vars + $this->vars;
		}
		
		return $this;
	}
	
	/**
	 * Magic method to set the variables in the private storage array
	 * 
	 * @param string $key   The variable name
	 * @param mixed  $value The variable value
	 * 
	 * @return this
	 */
	public final function __set($key, $value){
		$this->vars[$key] = $value;
		
		return $this;
	}
	
	/**
	 * Magic method to get the variables from the private storage array
	 *  
	 * @param $key The variable name
	 * 
	 * @return mixed
	 */
	public final function __get($key){
		return (array_key_exists($key, $this->vars) ? $this->vars[$key] : null);
	}
	
	/**
	 * Magic method to render the view if it should get echoed, shows a error if it gets a exeption in the rendering
	 * 
	 * @return string
	 */
	public final function __toString(){
		try{
			return $this->render();
		}
		catch(\Exception $e){
			echo 'Error: '.$e->getMessage();
			exit;
		}
	}
	
	/**
	 * Helper method to get the router from the view
	 * 
	 * @return Core\Router
	 */
	public function getRouter(){
		return Registry::get('router');
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
		return $this->getRouter()->url($params, $route, $reset);
	}
	
	/**
	 * Helper function to create a url to a css file
	 * 
	 * @param string $file CSS file name beginning from /public/css/ folder (without beginning slash!)
	 * 
	 * @return string
	 */
	public function css($file){
		return URL_PREFIX.'/css/'.$file;
	}

	/**
	 * Helper function to create a url to a js file
	 * 
	 * @param string $file Js file name beginning from /public/js/ folder (without beginning slash!)
	 * 
	 * @return string
	 */
	public function js($file){
		return URL_PREFIX.'/js/'.$file;
	}
	
	/**
	 * Helper function to create a url to a img file
	 * 
	 * @param string $file Img file name beginning from /public/img/ folder (without beginning slash!)
	 * 
	 * @return string
	 */
	public function img($file){
		return URL_PREFIX.'/img/'.$file;
	}
	
}

?>