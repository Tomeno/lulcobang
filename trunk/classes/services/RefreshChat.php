<?php

class RefreshChat {
	
	public function main() {
		$room = intval($_POST['room']);
		$game = intval($_POST['game']);
		
		$loggedUser = LoggedUser::whoIsLogged();
		$lastActivity = Room::getUserLastActivityInRoom($loggedUser['id'], $room);
		Room::updateUserLastActivity($loggedUser['id'], $room);
		
		MySmarty::assign('messages', Chat::getMessages($room, $loggedUser['id'], 0, $game));
		echo MySmarty::fetch('message-box.tpl');
	}
}

?>