<?php

class RoomListingBox extends AbstractBox {

	protected $template = 'rooms.tpl';

	protected function setup() {
		$loggedUser = LoggedUser::whoIsLogged();

		if (Utils::post('create_room') && $loggedUser['admin']) {
			$params = array(
				'title' => Utils::post('title'),
				'alias' => Utils::createAlias(Utils::post('title'), 'room'),
				'description' => Utils::post('description'),
			);
			$room = new Room($params);
			$room->save();
		}

		$roomRepository = new RoomRepository();
		$rooms = $roomRepository->getAll();
		
		$gameRepository = new GameRepository();
		$games = $gameRepository->getGamesByRooms(array_keys($rooms));
		
		foreach ($games as $game) {
			$rooms[$game['room']]['game'] = TRUE;
			$rooms[$game['room']]['status'] = Localize::getMessage('room_status_' . $game['status']);
		}

		MySmarty::assign('loggedUser', $loggedUser);
		MySmarty::assign('rooms', $rooms);
	}
}

?>