<?php

class SettingsBox extends AbstractBox {
	protected $template = 'settings.tpl';
	
	protected function setup() {
		$loggedUser = LoggedUser::whoIsLogged();
		if (Utils::post('change_settings')) {
			// TODO check if password and confirm password match
			$loggedUser['password'] = md5(Utils::post('password'));
			$loggedUser['name'] = Utils::post('name');
			$loggedUser['surname'] = Utils::post('surname');
			$loggedUser['color'] = intval(Utils::post('color'));
			$loggedUser->save();
		}
		MySmarty::assign('loggedUser', $loggedUser);

		$colorRepository = new ColorRepository();
		$colors = $colorRepository->getAll();
		MySmarty::assign('colors', $colors);
	}
}

?>