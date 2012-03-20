<?php

class LoginAction extends AbstractAction {

	public function getContent() {
		if (LoggedUser::whoIsLogged()) {
			if (isset($_COOKIE['ref_url'])) {
				$url = $_COOKIE['ref_url'];
				$absolute = FALSE;
			} else {
				$page = PageActionMap::getPageByTypeAndLanguage('rooms');
				$url = PageActionMap::createUrl($page['alias']);
				$absolute = TRUE;
			}

			Utils::redirect($url, $absolute);
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