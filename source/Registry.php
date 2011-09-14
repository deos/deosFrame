<?php 

namespace Core;

/**
 * Global registry class for storing data global, only used static
 * 
 * @package deosFrame
 * @author deos
 */
abstract class Registry {

	/**
	 * Data storage array
	 * 
	 * @var array(mixed)
	 */
	private static $data = array();
	
	/**
	 * Get stored data
	 * 
	 * @param string $name Name the data got stored as
	 * 
	 * @return mixed
	 */
	public static function get($name){
		if(isset(self::$data[$name])){
			return self::$data[$name];
		}
		throw new Exception('Not stored in registry: '.$name);
	}
	
	/**
	 * Stora data
	 * 
	 * @param string $name  Name the data get stored as
	 * @param mixed  $value The data to store
	 * 
	 * @return void
	 */
	public static function set($name, $value){
		self::$data[$name] = $value;
	}

}

?>