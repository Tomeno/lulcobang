<?php

class JailCommand extends Command {
	
	protected $attackedPlayer = NULL;

	const OK = 1;
	
	const CANNOT_PLAY_JAIL_AGAINST_DEAD_PLAYER = 2;
	
	const ALREADY_IN_JAIL = 3;
	
	const CANNOT_PLAY_JAIL_AGAINST_SHERIFF= 4;
	
	const CANNOT_PLAY_JAIL_AGAINST_YOURSELF = 5;
	
	const PLAYER_IS_NOT_IN_GAME = 6;
	
	protected function check() {
		// TODO create as checker
		$attackedPlayer = $this->params[0];
		if ($this->loggedUser['username'] != $attackedPlayer) {
			foreach ($this->players as $player) {
				$user = $player->getUser();
				if ($user['username'] == $attackedPlayer) {
					$this->attackedPlayer = $player;
					break;
				}
			}

			if ($this->attackedPlayer) {
				if ($this->attackedPlayer->getRoleObject()->getIsSheriff()) {
					$this->check = self::CANNOT_PLAY_JAIL_AGAINST_SHERIFF;
				} elseif ($this->attackedPlayer->getHasJailOnTheTable()) {
					$this->check = self::ALREADY_IN_JAIL;
				} elseif ($this->attackedPlayer['actual_lifes'] <= 0) {
					$this->check = self::CANNOT_PLAY_JAIL_AGAINST_DEAD_PLAYER;
				} else {
					$this->check = self::OK;
				}
			} else {
				$this->check = self::PLAYER_IS_NOT_IN_GAME;
			}
		} else {
			$this->check = self::CANNOT_PLAY_JAIL_AGAINST_YOURSELF;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			GameUtils::moveCards($this->game, $this->cards, $this->actualPlayer, 'table', $this->attackedPlayer);
		}
	}

	protected function generateMessages() {
		if ($this->attackedPlayer) {
			$attackedUser = $this->attackedPlayer->getUser();
		}
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' uvaznil ' . $attackedUser['username'],
				'notToUser' => $attackedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'uvaznil si ' . $attackedUser['username'],
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_PLAY_JAIL_AGAINST_DEAD_PLAYER) {
			$message = array(
				'text' => 'nemozes uvaznit mrtveho hraca',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::ALREADY_IN_JAIL) {
			$message = array(
				'text' => $attackedUser['username'] . ' uz je vo vazeni, nemozes ho znovu uvaznit',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_PLAY_JAIL_AGAINST_SHERIFF) {
			$message = array(
				'text' => 'nemozes uvaznit serifa',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_PLAY_JAIL_AGAINST_YOURSELF) {
			$message = array(
				'text' => 'nemozes uvaznit sam seba',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::PLAYER_IS_NOT_IN_GAME) {
			$message = array(
				'text' => 'hrac "' . $this->params[0] . '" nehra v tejto hre',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {

	}
}

?>