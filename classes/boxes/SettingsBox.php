<?php

class SettingsBox extends AbstractBox {
	protected $template = 'settings.tpl';
	
	protected function setup() {
		$loggedUser = LoggedUser::whoIsLogged();
		if (Utils::post('change_settings')) {
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