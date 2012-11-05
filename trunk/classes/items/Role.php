<?php

class Role extends LinkableItem {
	
	protected $imageFolder = 'images/cards/bang/roles/';
	protected $back = 'images/cards/bang/special/role_back.jpg';
	
	// TYPY
	const SHERIFF = 1;
	const RENEGARD = 2;
	const BANDIT = 3;
	const VICE = 4;
	
	// konretne idecka pre roly
	const ROLE_SHERIFF = 1;
	const ROLE_RENEGARD_1 = 2;
	const ROLE_BANDIT_1 = 3;
	const ROLE_BANDIT_2 = 4;
	const ROLE_VICE_1 = 5;
	const ROLE_BANDIT_3 = 6;
	const ROLE_VICE_2 = 7;
	const ROLE_RENEGARD_2 = 8;
	
	public function __construct($role) {
		parent::__construct($role);
	}

	public function getImagePath() {
		return $this->imageFolder . $this['image'];
	}

	public function getBackImagePath() {
		return $this->back;
	}

	public function getPageType() {
		return 'role';
	}

	public function getItemAlias() {
		return $this['alias'];
	}

	public function getRelatedRoles() {
		$roleRepository = new RoleRepository();
		$roleRepository->addAdditionalWhere(array('column' => 'type', 'value' => $this['type'], 'xxx' => '!='));
		$roleRepository->addGroupBy('type');
		return $roleRepository->getAll();
	}

	public function getLocalizedTitle() {
		return Localize::getMessage($this['localize_title_key']);
	}

	public function getLocalizedDescription() {
		return Localize::getMessage($this['localize_description_key']);
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
	
//	public function getBack() {
//		return $this->back;
//	}
}

?>