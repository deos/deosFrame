<?php 

use Core\Init;

$init = new \Core\Init();

//change some settings in dev mode
if(APPLICATION_ENV=='development'){
	$init->setAutoGenerateProxyClasses(true)
		->setCache('array')
		->setSQLLogger();
}

//set connection params
$init->setConnection(array(
	'driver'		=> 'pdo_mysql',
	'host'			=> 'localhost',
	'user'			=> 'root',
	'password'		=> '',
	'dbname'		=> 'demo'
));

//set routes (not needed, default route is always there, see below)
$init->setRoutes(array(
	//some simple admin route, it just beginns with admin and the folders have a prefix folder
	'admin' => array(
		'pattern' => '/admin/:module/:controller/:action/',
		'defaults' => array(
			'prefix' => 'admin'
		)
	),
	//in this route not all params are in the url itself but some are just defaults. but there are some other params in the route, user id and name. this is not always the usual way but it is to show how it works
	'user' => array(
		'pattern' => '/user/:userId/:userName/:action/',
		'defaults' => array(
			'module' => 'users',
			'controller' => 'details'
		)
	),
	//this is the default route. it does not need to be set since it gets set automatically but for showing it im using it here
	'default' => array(			
		'pattern' => '/:module/:controller/:action'	 
	)
));

//lets go
$init->run();

?>