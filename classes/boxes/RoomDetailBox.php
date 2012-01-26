<?php

class RoomDetailBox extends AbstractBox {
	
	protected $template = 'room.tpl';
	
	protected function setup() {
		$roomAlias = Utils::get('identifier');
		
		$roomRepository = new RoomRepository();
		$room = $roomRepository->getOneByAlias($roomAlias);

		if ($room) {
			$gameRepository = new GameRepository();
			$game = $gameRepository->getOneByRoom($room['id']);

			if (Utils::post()) {
				if (trim(Utils::post('message'))) {
					if (strpos($_POST['message'], '.') === 0) {
						$response = Command::setup($_POST['message'], $game);
					} else {
						$messageParams = array(
							'text' => $_POST['message'],
							'room' => $room['id'],
						);
						Chat::addMessage($messageParams);
					}
					Room::updateUserLastActivity($loggedUser['id'], $room['id']);
				} elseif (Utils::post('create')) {
					$response = Command::setup('.create', $game);
				} elseif (Utils::post('join')) {
					$response = Command::setup('.join', $game);
				} elseif (Utils::post('start')) {
					$response = Command::setup('.start', $game);
				}

				// TODO tu by sa mohol spravit redirect asi lebo respons bude v db
				MySmarty::assign('response', $response);
			}

			$gameBox = new GameBox();
			if ($game !== NULL) {
				$gameBox->setGame($game);
			}
			MySmarty::assign('gameBox', $gameBox->render());

			$chatBox = new ChatBox();
			$chatBox->setRoom($room);
			MySmarty::assign('chatBox', $chatBox->render());
		} else {
			// TODO 404 room not found
		}
	}
}

?>