<?php

class RefreshChat {
	
	public function main() {
		$room = intval($_POST['room']);
		
		$loggedUser = LoggedUser::whoIsLogged();
		$lastActivity = Room::getUserLastActivityInRoom($loggedUser['id'], $room);
		Room::updateUserLastActivity($loggedUser['id'], $room);
		
		MySmarty::assign('messages', Chat::getMessages($room, $loggedUser['id'], $lastActivity));
		echo MySmarty::fetch('message-box.tpl');
	}
}

$service = new RefreshChat();
$service->main();

?>