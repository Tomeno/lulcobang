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
						$response = Command::setup('command=' . substr($message, 1), $game);
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
					$response = Command::setup('command=create', $game);
				} elseif (Utils::post('join')) {
					$response = Command::setup('command=join', $game);
				} elseif (Utils::post('add_ai_player')) {
					$response = Command::setup('command=add_ai_player', $game);
				} elseif (Utils::post('start')) {
					$response = Command::setup('command=init', $game);
				} elseif (Utils::post('choose_character')) {
					$response = Command::setup('command=choose_character&selectedCharacter=' . Utils::post('character'), $game);
				} elseif (Utils::post('choose_cards')) {
					$commandParams = array();
					$commandParams['command'] = 'choose_cards';
					$commandParams['selectedCards'] = array();
					if (Utils::post('card')) {
						$commandParams['selectedCards'] = implode(',', Utils::post('card'));
					}
					$params = array();
					foreach ($commandParams as $key => $value) {
						$params[] = $key . '=' . $value;
					}
					$commandString = implode('&', $params);
					$response = Command::setup($commandString, $game);
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