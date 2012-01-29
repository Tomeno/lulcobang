<?php

class LogoutAction extends AbstractAction {

	public function getContent() {
		$loggedUser = LoggedUser::whoIsLogged();
		if ($loggedUser !== NULL) {
			LoggedUser::userLogout();
		}

		Utils::redirect(Utils::getActualUrl(), FALSE);
	}
}

?>