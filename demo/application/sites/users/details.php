<?php 

namespace Controller\Users;

use Core\Controller,
	Demo\Model\User;

class DetailsController extends Controller {
	
	public function init(){
		$this->view->user = User::find($this->request->getParameter('userId'));
	}
	
	public function indexAction(){
	}
	
}

?>