<?php

namespace Core;

use Doctrine\ORM\EntityManager;

/**
 * Global model class for doctrine, every model should extend it
 * 
 * @package deosFrame
 * @author deos
 * 
 * @MappedSuperclass
 */
abstract class Model {

	/**
	 * Class constructor
	 */
	public function __construct(){
		$this->init();
	}

	/**
	 * Init function for initializing some stuff in the model if needed
	 * 
	 * @return void
	 */
	public function init(){
		//overwrite me
	}
	
	/**
	 * Doctrine persist helper method
	 * 
	 * @return this
	 */
	public final function persist(){
		Registry::get('doctrine')->persist($this);
		
		return $this;
	}

	/**
	 * Doctrine persist and flush helper method
	 * 
	 * @return this
	 */
	public final function save(){
		$this->persist();
		Registry::get('doctrine')->flush();
		
		return $this;
	}	

	/**
	 * Doctrine remove helper method
	 * 
	 * @return this
	 */
	public final function remove(){
		Registry::get('doctrine')->remove($this);
		
		return $this;
	}

	/**
	 * Magic method to allow getAttribute calls for the doctrine objects
	 * 
	 * @param string $method    Method name
	 * @param array  $arguments Passed arguments
	 * 
	 * @return mixed
	 */
	public function __call($method, $arguments){
		$split = preg_split('/([A-Z])/', $method, null, PREG_SPLIT_NO_EMPTY  | PREG_SPLIT_DELIM_CAPTURE);

		if(count($split)>=3){ 
			$action = array_shift($split);
			$property = lcfirst(implode('', $split));
		
			if(!property_exists($this, $property)){
				throw new Exception('Property '.$property.' not found');
			}
			
			switch($action){
				case 'set':
					return $this->$property = $arguments[0];
					break;
					
				case 'get':
					return $this->$property;
					break;
			}
		}
				
		throw new Exception('Method '.$method.' not found');
	}

	/**
	 * Magic method for static calls, hands them over to doctrine to make easy requests
	 * 
	 * @param string $method    Static call method name
	 * @param array  $arguments Static call method arguments
	 * 
	 * @return mixed
	 */
	public static function __callStatic($method, $arguments){
		return call_user_func_array(array(Registry::get('doctrine')->getRepository(get_called_class()), $method), $arguments);
	}

}
