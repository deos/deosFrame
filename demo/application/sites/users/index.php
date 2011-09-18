<?php 

namespace Controller\Users;

use Core\Controller,
	Demo\Model\User;

class IndexController extends Controller {
	
	public function init(){
	}
	
	public function indexAction(){
		$this->view->users = User::findAll();
	}
	
}

?>