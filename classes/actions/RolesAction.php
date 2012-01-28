<?php

class RolesAction extends AbstractAction {

	public function getContent() {
		if (Utils::get('identifier')) {
			$page = PageActionMap::getPageByTypeAndLanguage('role');
			if ($page['alias'] != Utils::get('action')) {
				$url = PageActionMap::createUrl(array($page['alias'], Utils::get('identifier')), Utils::get('language'));
				Utils::redirect($url);
			}
			$box = new RoleDetailBox();
		} else {
			$page = PageActionMap::getPageByTypeAndLanguage('roles');
			if ($page['alias'] != Utils::get('action')) {
				$url = PageActionMap::createUrl(array($page['alias']), Utils::get('language'));
				Utils::redirect($url);
			}
			$box = new RoleListingBox();
		}
		return $box->render();
	}
}

?>