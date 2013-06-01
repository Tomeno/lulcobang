<?php

class BountyCommand extends Command {
	
	const OK = 1;
	
	const CANNOT_PLAY_BOUNTY_AGAINST_DEAD_PLAYER = 2;
	
	const PLAYER_IS_NOT_IN_GAME = 3;
	
	protected function check() {
		if ($this->attackedPlayer) {
			if (!$this->attackedPlayer->getIsAlive()) {
				$this->check = self::CANNOT_PLAY_BOUNTY_AGAINST_DEAD_PLAYER;
			} else {
				$this->check = self::OK;
			}
		} else {
			$this->check = self::PLAYER_IS_NOT_IN_GAME;
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
				'text' => $this->loggedUser['username'] . ' vypísal odmenu na hráča' . $attackedUser['username'],
				'notToUser' => $attackedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'vypísal si odmenu na hráča' . $attackedUser['username'],
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_PLAY_BOUNTY_AGAINST_DEAD_PLAYER) {
			$message = array(
				'text' => 'nemozes vypísať odmenu na mrtveho hraca',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::PLAYER_IS_NOT_IN_GAME) {
			$message = array(
				'text' => 'hrac "' . $this->params['enemyPlayerUsername'] . '" nehra v tejto hre',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {

	}
}

?>