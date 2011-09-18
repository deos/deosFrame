<?php 

//define base constants
define('BASE_PATH', str_replace(DIRECTORY_SEPARATOR.'public', '', __DIR__));
define('APP_PATH', BASE_PATH.DIRECTORY_SEPARATOR.'application');
define('FW_PATH', BASE_PATH.DIRECTORY_SEPARATOR.'frameworks');
define('URL_PREFIX', rtrim(dirname($_SERVER['PHP_SELF']), '/\\'));

define('APPLICATION_ENV', 'development');


//init autoloader
spl_autoload_register(function($className){
	if(substr($className, 0, 11)=='Controller\\'){
		$path = APP_PATH.DIRECTORY_SEPARATOR.'sites'.DIRECTORY_SEPARATOR.strtolower(str_replace(array('Controller'.DIRECTORY_SEPARATOR, 'Controller'), '', str_replace('\\', DIRECTORY_SEPARATOR, $className)).'.php');
	}
	else{
		$path = FW_PATH.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $className).'.php';
	}
	
	if(file_exists($path)){
		require($path);
	}
	else{
		throw new Exception('Could not load class '.$path.'!!');
	}
});


//echo helper function
function e() {
	echo utf8_encode(htmlspecialchars(implode('', func_get_args())));
}


//run init
require(APP_PATH.DIRECTORY_SEPARATOR.'init.php');

?>