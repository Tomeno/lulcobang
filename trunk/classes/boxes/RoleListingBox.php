<?php

class RoleListingBox extends AbstractBox {

	protected $template = 'role-listing.tpl';

	protected function setup() {
		$roleRepository = new RoleRepository(TRUE);
		$roleRepository->addGroupBy('type');
		$roles = $roleRepository->getAll();

		MySmarty::assign('roles', $roles);
	}
}

?>