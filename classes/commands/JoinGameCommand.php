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
			DB::insert('player', $params);
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$this->messages[] = array(
				'user' => User::SYSTEM,
				'notToUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'player_joined_game',
				'localizeParams' => array($this->loggedUser['username']),
			);

			$this->messages[] = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'you_joined_game',
				'localizeParams' => array($this->loggedUser['username']),
			);
		} elseif ($this->check == self::ALREADY_JOINED) {
			$this->messages[] = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'already_joined',
			);
		} elseif ($this->check == self::GAME_STARTED) {
			$this->messages[] = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'cannot_join_game_already_started',
			);

		} elseif ($this->check == self::NO_GAME) {
			$this->messages[] = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'cannot_join_no_game_in_room',
			);
		}
	}

	protected function createResponse() {
		return '';
	}
}

?>