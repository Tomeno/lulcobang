<?php

class Role extends Item {
	
	protected $imageFolder = 'images/cards/bang/roles/';
	
	const SHERIFF = 1;
	const RENEGARD = 2;
	const BANDIT = 3;
	const VICE = 4;
	
	public function __construct($role) {
		parent::__construct($role);
	}
	
	public function getIsSheriff() {
		if ($this['type'] == Role::SHERIFF) {
			return true;
		}
		return false;
	}
	
	public function getIsRenegard() {
		if ($this['type'] == Role::RENEGARD) {
			return true;
		}
		return false;
	}
	
	public function getIsBandit() {
		if ($this['type'] == Role::BANDIT) {
			return true;
		}
		return false;
	}
	
	public function getIsVice() {
		if ($this['type'] == Role::VICE) {
			return true;
		}
		return false;
	}
}

?>