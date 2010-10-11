<?php

require_once('../include.php');

class RefreshUsersBox {
	
	public function main() {
		$room = intval($_POST['room']);
		$loggedUser = User::whoIsLogged();
		
		$GLOBALS['smarty']->assign('users', Room::getUsers($room));
		echo $GLOBALS['smarty']->fetch('users-box.tpl');
	}
}

$service = new RefreshUsersBox();
$service->main();

?>