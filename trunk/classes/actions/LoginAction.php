<?php

class LoginAction extends AbstractAction {

	public function getContent() {
		if (LoggedUser::whoIsLogged()) {
			Utils::redirect('miestnosti.html');
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