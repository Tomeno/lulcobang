<?php

class ClickLogger {
	
	public function main() {

		// TODO priamo v hre mi neberie kliknutia akonahle sa vyrenderuju tipy, pravdepodobne to suvisi s tym z-indexom

		$x = intval($_POST['x']);
		$y = intval($_POST['y']);
		$url = addslashes($_POST['url']);

		$loggedUser = LoggedUser::whoIsLogged();
		$params = array(
			'coord_x' => $x,
			'coord_y' => $y,
			'url' => $url,
			'ip' => $_SERVER['REMOTE_ADDR'],
			'browser' => $_SERVER['HTTP_USER_AGENT'],
			'user' => $loggedUser['id'],
		);
		DB::insert('click', $params);
	}
}

?>