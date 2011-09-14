<?php 

namespace Core;

/**
 * Helper class for everything request related, inclusive getting post and get variables
 *  
 * @package deosFrame
 * @author deos
 */
final class Request {

	/**
	 * Get a get variable
	 * 
	 * @param string $name        Get variable name
	 * @param mixed  $notsetValue Value if get variable is not set
	 * 
	 * @return mixed
	 */
	public function get($name, $notsetValue = null){
		return (isset($_GET[$name]) ? $_GET[$name] : $notsetValue);
	}

	/**
	 * Get a post variable
	 * 
	 * @param string $name        Post variable name
	 * @param mixed  $notsetValue Value if post variable is not set
	 * 
	 * @return mixed
	 */
	public function post($name, $notsetValue = null){
		return (isset($_POST[$name]) ? $_POST[$name] : $notsetValue);
	}

	/**
	 * Get a parameter from the current path configuration (from router)
	 * 
	 * @param string $name Parameter name
	 * 
	 * @return mixed
	 */
	public function getParameter($name){
		return Registry::get('router')->get($name);
	}
	
    /**
     * check if a post form got submitted
     * 
     * @return boolean
     */
    public function isPost() {
        return (isset($_SERVER['REQUEST_METHOD']) AND $_SERVER['REQUEST_METHOD']=='POST');
    } 	
	
	/**
	 * Check if the current call is a ajax call
	 * 
	 * @return boolean
	 */
    public function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest');
    }

}

?>