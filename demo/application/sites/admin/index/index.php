<?php 

namespace Controller\Admin\Index;

//we use another controller for all admin pages
use Demo\Controller\Admin;

class IndexController extends Admin {
	
	//no init function or use parent::init() in it so the init in the admin controller gets called for sure
	
	public function indexAction(){
	}
}

?>
