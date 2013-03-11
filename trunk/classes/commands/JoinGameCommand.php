<?php

class JoinGameCommand extends Command {

	const OK = 1;
	const ALREADY_JOINED = 2;
	const GAME_STARTED = 3;
	const NO_GAME = 4;

	protected function check() {
		if ($this->game) {
			if ($this->game['status'] == 0) {
				$userCount = GameUtils::checkUserInGame($this->loggedUser, $this->game);

				if ($userCount > 0) {
					$this->check = self::ALREADY_JOINED;
				} else {
					$this->check = self::OK;
				}
			} else {
				$this->check = self::GAME_STARTED;
			}
		} else {
			$this->check = self::NO_GAME;
		}
	}

	protected function run() {
		if ($this->check === self::OK) {
			$playersCount = GameUtils::getPosition($this->game);

			$params = array(
				'game' => $this->game['id'],
				'user' => $this->loggedUser['id'],
				'seat' => GameUtils::getSeatOnPosition($playersCount),
			);
			// TODO use repository
			DB::insert(DB_PREFIX . 'player', $params);
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'localizeKey' => 'player_joined_game',
				'localizeParams' => array($this->loggedUser['username']),
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'localizeKey' => 'you_joined_game',
				'localizeParams' => array($this->loggedUser['username']),
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::ALREADY_JOINED) {
			$message = array(
				'localizeKey' => 'already_joined',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::GAME_STARTED) {
			$message = array(
				'localizeKey' => 'cannot_join_game_already_started',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NO_GAME) {
			$message = array(
				'localizeKey' => 'cannot_join_no_game_in_room',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
		return '';
	}
}

?>