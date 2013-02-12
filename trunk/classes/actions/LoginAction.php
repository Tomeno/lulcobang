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
			MySmarty::assign('username', Utils::post('username'));
			$validation = LoggedUser::userLogin();
			//var_Dump($validation);exit();
			if ($validation === TRUE) {
				Utils::redirect(Utils::getActualUrlWithoutGetParameters(), FALSE);
			} else {
				MySmarty::assign('errors', $validation);
			}
		}

		return MySmarty::fetch('login.tpl');
	}
}

?>