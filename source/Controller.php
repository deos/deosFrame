<?php 

namespace Core;

/**
 * Basic controller class, every controller has to extend it
 * 
 * @package deosFrame
 * @author deos
 */
abstract class Controller {
	
	/**
	 * Renderstatus, if false there will be no view rendered
	 * 
	 * @var boolean
	 */
	private $renderStatus = true;
	
	/**
	 * View used as template
	 * 
	 * @var \Core\View
	 */
	private $template;
	
	/**
	 * View object
	 * 
	 * @var \Core\View
	 */
	public $view;
	
	/**
	 * Request object
	 * 
	 * @var \Core\Request
	 */
	public $request;
		
	/**
	 * Class constructor
	 * 
	 * @param string     $action   Called action name
	 * @param \Core\View $template View used as template
	 */
	public final function __construct($action, View $template){
		$this->view = new View('/sites/'.strtolower(str_replace(array('Controller/', 'Controller'), '', str_replace('\\', '/', get_class($this)))).'/'.$action.'.html.php');
		$this->request = new Request();
		$this->template = $template;
		
		$this->init();
	}
	
	/**
	 * Init function that gets called after the constructor is done
	 * 
	 * @return void
	 */
	protected function init(){
		//overwrite me
	}
	
	/**
	 * Change the view to another one
	 * 
	 * @param \Core\View $view the new view object to use
	 * 
	 * @return this
	 */
	public final function setView(View $view){
		$view->setVars($this->view->getVars());
		$this->view = $view;
		
		return $this;
	}
	
	/**
	 * Get current view object
	 * 
	 * @return \Core\View
	 */
	public final function getView(){
		return $this->view;
	}
	
	/**
	 * Change the template to another one
	 * 
	 * @param \core\View $template Template view
	 * 
	 * @return this
	 */
	public final function setTemplate(View $template = null){
		$this->template = $template;
		
		return $this;
	}
	
	/**
	 * Set render status, if render staut is false the view is not rendered
	 * 
	 * @param boolean $status The status to set
	 * 
	 * @return this
	 */
	public final function setRenderStatus($status){
		$this->renderStatus = !!$status;
		
		return $this;
	}
	
	/**
	 * Render the view with its template and all
	 * 
	 * @return string|null
	 */
	public final function render(){
		if($this->renderStatus){
			if($this->template){
				$this->template->setVars($this->view->getVars());
				$this->template->content = $this->view->render();
				return $this->template->render();
			}
			return $this->view->render();
		}
		return null;
	}
	
	/**
	 * Do a header redirect
	 *  
	 * @param string $url Url to riderect to
	 * 
	 * @return void
	 */
	public final function redirect($url){
		Registry::get('router')->redirect($url);	
	}

}

?>