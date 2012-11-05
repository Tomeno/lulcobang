<?php

class BeerCommand extends Command {
	
	const OK = 1;
	
	const TOO_MANY_LIFES = 2;
	
	const ONLY_TWO_PLAYERS_IN_GAME = 3;
	
	protected function check() {
		$livePlayers = 0;
		foreach ($this->getPlayers() as $player) {
			if ($player['actual_lifes'] > 0) {
				$livePlayers++;
			}
		}
		
		if ($livePlayers > 2) {
			if ($this->actualPlayer['actual_lifes'] < $this->actualPlayer['max_lifes']) {
				$this->check = self::OK;
			} else {
				$this->check = self::TOO_MANY_LIFES;
			}
		} else {
			$this->check = self::ONLY_TWO_PLAYERS_IN_GAME;
		}
		// TODO check reverend
	}

	protected function run() {
		if ($this->check == self::OK) {
			$additionalLifes = 1;
			if ($this->actualPlayer->getCharacter()->getIsTequilaJoe()) {
				$additionalLifes = 2;
			}
			$newLifes = min($this->actualPlayer['actual_lifes'] + $additionalLifes, $this->actualPlayer['max_lifes']);
			$this->actualPlayer['actual_lifes'] = $newLifes;
			$this->actualPlayer->save();

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil pivo na doplnenie zivota',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si pivo na doplnenie zivota',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::TOO_MANY_LIFES) {
			$message = array(
				'text' => 'nemozes pouzit pivo, mas plnu nadrz',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::ONLY_TWO_PLAYERS_IN_GAME) {
			$message = array(
				'text' => 'nemozes pouzit pivo, hrate uz len dvaja',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
		;
	}
}

?>