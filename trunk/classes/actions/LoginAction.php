<?php

class LoginAction extends AbstractAction {

	public function getContent() {
		if (LoggedUser::whoIsLogged()) {
			$page = PageActionMap::getPageByTypeAndLanguage('rooms');
			$url = PageActionMap::createUrl($page['alias']);

			Utils::redirect($url);
		}

		if (Utils::post('login')) {
			MySmarty::assign('formSent', TRUE);
			MySmarty::assign('username', Utils::post('username'));
			LoggedUser::userLogin();
			if (LoggedUser::whoIsLogged()) {
				MySmarty::assign('loginSuccessfull', TRUE);
			}
		}

		return MySmarty::fetch('login.tpl');
	}
}

?>