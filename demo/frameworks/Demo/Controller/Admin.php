<?php 

namespace Demo\Controller;

use Core\Controller,
	Core\View;

class Admin extends Controller {
	
	public function init(){
		//how about placing some security checks here?
		
		//add a admin submenu for all admin pages
		$this->view->submenu = new View('/sites/admin/menu.html.php');
	}
	
}


?>