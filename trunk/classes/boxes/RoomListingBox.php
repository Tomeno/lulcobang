<?php

class RoomListingBox extends AbstractBox {

	protected $template = 'rooms.tpl';

	protected function setup() {
		if ($_POST['create_room'] && $loggedUser['admin']) {
			throw new Exception('NOT YET IMPLEMENTED', 1324243936);
			$room = Room::addRoom(addslashes($_POST['title']), addslashes($_POST['description']));
			Utils::redirect('chat/' . $room . '.html');
		}

		$roomRepository = new RoomRepository();
		$rooms = $roomRepository->getAll();

		MySmarty::assign('loggedUser', $loggedUser);
		MySmarty::assign('rooms', $rooms);
	}
}

?>