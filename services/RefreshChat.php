<?php

require_once('../include.php');

class RefreshChat {
	
	public function main() {
		$room = intval($_POST['room']);
		
		$loggedUser = User::whoIsLogged();
		$lastActivity = Room::getUserLastActivityInRoom($loggedUser['id'], $room);
		
		$GLOBALS['smarty']->assign('messages', Chat::getMessages($room, $loggedUser['id'], $lastActivity));
		echo $GLOBALS['smarty']->fetch('message-box.tpl');
	}
}

$service = new RefreshChat();
$service->main();

?>