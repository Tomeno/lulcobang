<?php

class JoinGameCommand extends Command {

	const OK = 1;
	const ALREADY_JOINED = 2;
	const GAME_STARTED = 3;
	const NO_GAME = 4;

	protected function check() {
		if ($this->game) {
			if ($game['status'] == 0) {
				$userCount = GameUtils::checkUserInGame($this->loggedUser, $this->game);

				if ($userCount > 0) {
					$this->check = self::ALREADY_JOINED;
				} else {
					$this->check = self::OK;
				}
			} elseif ($this->game['status'] == 1) {
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

	protected function write() {
		if ($this->check == self::OK) {
			$messageParams = array(
				'user' => User::SYSTEM,
				'notToUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'player_joined_game',
				'localizeParams' => array($this->loggedUser['username']),
			);
			Chat::addMessage($messageParams);

			$messageParams = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'you_joined_game',
				'localizeParams' => array($this->loggedUser['username']),
			);
			Chat::addMessage($messageParams);
		} elseif ($this->check == self::ALREADY_JOINED) {
			$messageParams = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'already_joined',
			);
			Chat::addMessage($messageParams);
		} elseif ($this->check == self::GAME_STARTED) {
			$messageParams = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'cannot_join_game_already_started',
			);
			Chat::addMessage($messageParams);

		} elseif ($this->check == self::NO_GAME) {
			$messageParams = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'cannot_join_no_game_in_room',
			);
			Chat::addMessage($messageParams);
		}
	}

	protected function createResponse() {
		return '';
	}
}

?>