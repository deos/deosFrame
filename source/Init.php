<?php 

namespace Core;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration,
    Doctrine\Common\Cache\ApcCache,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\DBAL\Logging\DebugStack;

/**
 * Helper class to initialize doctrine and start the system
 * 
 * @package deosFrame
 * @author deos
 */
class Init {
	
	/**
	 * Doctrine configurations
	 * 
	 * @var Doctrine\ORM\Configuration
	 */
	private $doctrineConfig;
	
	/**
	 * Cache for doctrine
	 * 
	 * @var Doctrine\Common\Cache\Cache
	 */
	private $cache;
	
	/**
	 * Anotation driver for doctrine
	 * 
	 * @var Doctrine\ORM\Mapping\ClassMetadata
	 */
	private $anotationDriver;
	
	/**
	 * db connection data for doctrine 
	 * 
	 * @var array(string)
	 */
	private $dbConnection;
	
	/**
	 * Path routes
	 * 
	 * @var array(string)
	 */
	private $routes = array();

	/**
	 * Class constructor
	 */
	public function __construct(){
		$this->doctrineConfig = new Configuration();

		//some default stuff
		$this->setProxyDir(DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'doctrine'.DIRECTORY_SEPARATOR.'proxies');
		$this->setProxyNamespace('Proxy');
		$this->setAutoGenerateProxyClasses(false);
		$this->setCache();
		$this->setAnotationDriver();
	}
	
	/**
	 * Set doctrine proxy dir
	 * 
	 * @param string $path Proxy dir path beginning from BASE_PATH
	 * 
	 * @return this
	 */
	public function setProxyDir($path){
		$this->doctrineConfig->setProxyDir(BASE_PATH.$path);
		
		return $this;
	}
	
	/**
	 * Set doctrine proxy namespace
	 * 
	 * @param string $namespace Proxy namespace name
	 * 
	 * @return this
	 */
	public function setProxyNamespace($namespace){
		$this->doctrineConfig->setProxyNamespace($namespace);
		
		return $this;
	}
	
	/**
	 * Set auto generate proxy classes
	 * 
	 * @param boolean $state True or false if the proxies should be auto-generated (usually only true in dev mode) [default: true]
	 *  
	 * @return this
	 */
	public function setAutoGenerateProxyClasses($state = true){
		$this->doctrineConfig->setAutoGenerateProxyClasses($state);
		
		return $this;
	}
	
	/**
	 * Set doctrine cache
	 * 
	 * @param string $type Type of the cache to use (apc and array supported for now) [default: apc] 
	 * 
	 * @return this
	 */
	public function setCache($type = 'apc'){
		switch($type){
			case 'apc':
				if(extension_loaded('apc')){
					$this->cache = new ApcCache();
					break;
				}
			
			case 'array':
			default:
				$this->cache = new ArrayCache();
				break;
		}
		
		return $this;
	}
	
	/**
	 * Set doctrine anotation driver
	 * 
	 * @param string $type Type (currently only anotation supported) [default: anotation] 
	 * @param mixed  $path Path(s) for the driver [default: null]
	 * 
	 * @return this
	 */
	public function setAnotationDriver($type = 'anotation', $path = null) {
		switch($type){
			case 'anotation':
			default:
				$this->anotationDriver = $this->doctrineConfig->newDefaultAnnotationDriver($path);		
		}
		
		return $this;
	}
	
	/**
	 * Set doctrine connection configuration
	 * 
	 * @param array $dbConnection Connection config data 
	 * 
	 * @return this
	 */
	public function setConnection(array $dbConnection){
		$this->dbConnection = $dbConnection;
		
		return $this;
	}
	
	/**
	 * Set doctrine sql logger
	 * 
	 * @return this
	 */
	public function setSQLLogger(){
		$logger =  new DebugStack();
		$this->doctrineConfig->setSQLLogger($logger);
		
		return $this;
	}
	
	/**
	 * Set system path routes
	 * 
	 * @param array $routes Array with routes
	 * 
	 * @return this
	 */
	public function setRoutes(array $routes){
		$this->routes = $routes;
		
		return $this;
	}
	
	/**
	 * Init doctrine, router and start the system
	 * 
	 * @return void
	 */
	public function run(){
		if(!$this->dbConnection){
			die('Please set db connection data');
		}
		
		//set cache
		$this->doctrineConfig->setMetadataCacheImpl($this->cache);
		$this->doctrineConfig->setQueryCacheImpl($this->cache);
		$this->doctrineConfig->setResultCacheImpl($this->cache);
		
		//set anotation driver
		$this->doctrineConfig->setMetadataDriverImpl($this->anotationDriver);
		
		//init entity manager
		$entityManager = EntityManager::create($this->dbConnection, $this->doctrineConfig);
		Registry::set('doctrine', $entityManager);
		
		
		//start router
		$router = new Router($this->routes);
		Registry::set('router', $router);
		
		try{
			echo $router->start();
		}
		catch(\Exception $e){
			echo 'Something went terribly wrong!!';
			exit;
		}		
	}
}

?>