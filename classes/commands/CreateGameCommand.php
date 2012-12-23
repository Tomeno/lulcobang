<?php

class CreateGameCommand extends Command {

	protected function check() {
		$gameRepository = new GameRepository();
		$gameRepository->addAdditionalWhere(array('column' => 'room', 'value' => $this->room['id']));
		$gameRepository->addAdditionalWhere(array('column' => 'status', 'value' => array(Game::GAME_STATUS_CREATED, Game::GAME_STATUS_STARTED), 'xxx' => 'IN'));
		$count = $gameRepository->getCountAll();
		if ($count > 0) {
			$this->check = FALSE;
		} else {
			$this->check = TRUE;
		}
	}

	protected function run() {
		if ($this->check) {
			$params = array(
				'room' => $this->room['id'],
				'creator' => $this->loggedUser['id']
			);
			// TODO use repository
			DB::insert(DB_PREFIX . 'game', $params);
		}
	}

	protected function generateMessages() {
		if ($this->check) {
			$message = array(
				'user' => User::SYSTEM,
				'room' => $this->room['id'],
				'localizeKey' => 'game_created'
			);
		} else {
			$message = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'game_already_created'
			);
			
		}
		$this->messages[] = $message;
	}

	protected function createResponse() {
		return '';
	}
}

?>