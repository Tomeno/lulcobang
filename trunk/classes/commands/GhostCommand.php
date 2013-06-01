<?php

class GhostCommand extends Command {
	
	const OK = 1;
	
	const CANNOT_PLAY_GHOST_ON_ALIVE_PLAYER = 2;
	
	const PLAYER_IS_NOT_IN_GAME = 3;
	
	const CANNOT_PLAY_GHOST_AGAINST_YOURSELF = 4;
	
	protected function check() {
		$attackedPlayer = $this->params['enemyPlayerUsername'];
		if ($this->loggedUser['username'] != $attackedPlayer) {
			if ($this->attackedPlayer) {
				if ($this->attackedPlayer['actual_lifes'] > 0) {
					$this->check = self::CANNOT_PLAY_GHOST_ON_ALIVE_PLAYER;
				} else {
					$this->check = self::OK;
				}
			} else {
				$this->check = self::PLAYER_IS_NOT_IN_GAME;
			}
		} else {
			$this->check = self::CANNOT_PLAY_GHOST_AGAINST_YOURSELF;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			GameUtils::moveCards($this->game, $this->cards, $this->actualPlayer, 'table', $this->attackedPlayer);
			
			// preratame maticu kedze sa do hry vracia hrac
			$matrix = GameUtils::countMatrix($this->game);
			$this->game['distance_matrix'] = serialize($matrix);
			$this->game->save();
		}
	}

	protected function generateMessages() {
		if ($this->attackedPlayer) {
			$attackedUser = $this->attackedPlayer->getUser();
		}
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' spravil ducha z ' . $attackedUser['username'],
				'notToUser' => $attackedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'Spravil si ducha z ' . $attackedUser['username'],
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_PLAY_GHOST_ON_ALIVE_PLAYER) {
			$message = array(
				'text' => 'nemozes spravit ducha zo ziveho hraca',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_PLAY_GHOST_AGAINST_YOURSELF) {
			$message = array(
				'text' => 'nemozes spravit ducha sam zo seba',
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