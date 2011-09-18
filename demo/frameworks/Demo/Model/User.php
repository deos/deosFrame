<?php 

namespace Demo\Model;

use Core\Model;

/**
 * Demo user class
 * 
 * @Entity
 * @Table(name="users")
 */
class User extends Model {
	
	/**
	 * User id
	 * 
	 * @id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;
	
	/**
	 * User name
	 * 
	 * @Column(type="string")
	 */
	protected $name;
	
	/**
	 * Init script, gets called after object construction
	 */
	public function init(){
		//got some inits to do?
	}
	
}

?>