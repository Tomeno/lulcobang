<?php

class RoomListingBox extends AbstractBox {

	protected $template = 'rooms.tpl';

	protected function setup() {
		$loggedUser = LoggedUser::whoIsLogged();

		if (Utils::post('create_room') && $loggedUser['admin']) {
			throw new Exception('NOT YET IMPLEMENTED', 1324243936);
			$room = Room::addRoom(addslashes(Utils::post('title')), addslashes(Utils::post('description')));
			Utils::redirect('chat/' . $room . '.html');
		}

		$roomRepository = new RoomRepository();
		$rooms = $roomRepository->getAll();

		MySmarty::assign('loggedUser', $loggedUser);
		MySmarty::assign('rooms', $rooms);
	}
}

?>