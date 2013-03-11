<?php

class UpperPartBox extends AbstractBox {
	protected $template = 'upper-part.tpl';
	
	protected function setup() {
		$loggedUser = LoggedUser::whoIsLogged();
		if ($loggedUser) {
			MySmarty::assign('loggedUser', $loggedUser);
			$logoutPage = PageActionMap::getPageByTypeAndLanguage('logout');
			MySmarty::assign('logoutPage', $logoutPage);
		} else {
			$loginPage = PageActionMap::getPageByTypeAndLanguage('login');
			MySmarty::assign('loginPage', $loginPage);
		}
		MySmarty::assign('language', Utils::get('language'));
	}
}

?>