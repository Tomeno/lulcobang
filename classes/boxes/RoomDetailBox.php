<?php

class RoomDetailBox extends AbstractBox {
	
	protected $template = 'room.tpl';
	
	protected function setup() {
		$roomAlias = Utils::get('identifier');
		
		$roomRepository = new RoomRepository();
		$room = $roomRepository->getOneByAlias($roomAlias);
		$loggedUser = LoggedUser::whoIsLogged();

		if ($room) {
			$gameRepository = new GameRepository();
			// $gameRepository->addAdditionalWhere(array('column' => 'status', 'value' => Game::GAME_STATUS_ENDED, 'xxx' => '!='));
			$gameRepository->addOrderBy(array('id' => 'DESC'));
			$game = $gameRepository->getOneByRoom($room['id']);

			if (Utils::post()) {
				$message = addslashes(trim(Utils::post('message')));
				if ($message != '') {
					if (strpos($message, '.') === 0) {
						$response = Command::setup($message, $game);
					} else {
						$messageParams = array(
							'text' => $message,
							'room' => $room['id'],
							'game' => $game['id'],
						);
						Chat::addMessage($messageParams);
					}
					Room::updateUserLastActivity($loggedUser['id'], $room['id']);
				} elseif (Utils::post('create')) {
					$response = Command::setup('.create', $game);
				} elseif (Utils::post('join')) {
					$response = Command::setup('.join', $game);
				} elseif (Utils::post('start')) {
					$response = Command::setup('.init', $game);
				} elseif (Utils::post('choose_character')) {
					$response = Command::setup('.choose_character ' . Utils::post('character'), $game);
				} elseif (Utils::post('choose_cards')) {
					if (Utils::post('card')) {
						$params = ' ' . implode(' ', Utils::post('card'));
					}
					$response = Command::setup('.choose_cards' . $params , $game);
				}
				Utils::redirect(Utils::getActualUrl(), FALSE);
				// TODO tu by sa mohol spravit redirect asi lebo respons bude v db
				MySmarty::assign('response', $response);
			}

			$gameBox = new GameBox();
			$gameBox->setRoom($room);
			if ($game !== NULL) {
				$gameBox->setGame($game);
			}
			MySmarty::assign('gameBox', $gameBox->render());

			$chatBox = new ChatBox();
			$chatBox->setRoom($room);
			if ($game !== NULL) {
				$chatBox->setGame($game);
			}
			MySmarty::assign('chatBox', $chatBox->render());
		} else {
			// TODO 404 room not found
		}
	}
}

?>