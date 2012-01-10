<?php

class User extends Item {
	
	protected static $loggedUser = null;
	
	protected static $cookieName = 'bang_user';
	
	const SYSTEM = 1;
	
	public function __construct($user) {
		parent::__construct($user);
	}
}

?>