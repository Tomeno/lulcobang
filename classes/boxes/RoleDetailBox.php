<?php

class RoleDetailBox extends AbstractBox {

	protected $template = 'role-detail.tpl';

	protected function setup() {
		$roleAlias = Utils::get('identifier');

		$roleRepository = new RoleRepository(TRUE);
		$role = $roleRepository->getOneByAlias($roleAlias);

		MySmarty::assign('role', $role);
	}
}

?>