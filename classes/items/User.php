<?php

class User extends LinkableItem {
	
	protected static $loggedUser = null;
	
	protected static $cookieName = 'bang_user';
	
	const SYSTEM = 1;
	
	public function __construct($user) {
		parent::__construct($user);
	}

	protected function getItemAlias() {
		return $this['username'];
	}

	protected function getPageType() {
		return 'user';
	}

	public function getSettingsUrl() {
		$settingsPage = PageActionMap::getPageByTypeAndLanguage('user-settings');
		return PageActionMap::createUrl($settingsPage['alias']);
	}

	public function getFontColor() {
		$colorRepository = new ColorRepository();
		return $colorRepository->getOneById($this['color']);
	}
}

?>