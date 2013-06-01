<?php

class BeerCommand extends Command {
	
	const OK = 1;
	
	const TOO_MANY_LIFES = 2;
	
	const ONLY_TWO_PLAYERS_IN_GAME = 3;
	
	const REVEREND_IN_THE_GAME = 4;
	
	protected function check() {
		if ($this->game->getIsHNTheReverend()) {
			$this->check = self::REVEREND_IN_THE_GAME;
		} else {
			$livePlayers = 0;
			foreach ($this->getPlayers() as $player) {
				if ($player->getIsAlive()) {
					$livePlayers++;
				}
			}

			if ($livePlayers > 2) {
				//if ($this->actualPlayer['actual_lifes'] < $this->actualPlayer['max_lifes']) {
					$this->check = self::OK;
				//} else {
				//	$this->check = self::TOO_MANY_LIFES;
				//}
			} else {
				$this->check = self::ONLY_TWO_PLAYERS_IN_GAME;
			}
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			$additionalLifes = 1;
			if ($this->useCharacter === TRUE && $this->actualPlayer->getIsTequilaJoe()) {
				$additionalLifes = 2;
			}
			$newLifes = min($this->actualPlayer['actual_lifes'] + $additionalLifes, $this->actualPlayer['max_lifes']);
			$this->actualPlayer['actual_lifes'] = $newLifes;
			$this->actualPlayer->save();

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			
			foreach ($this->game->getPlayers() as $player) {
				if ($player->getIsMadamYto()) {
					$drawnCards = GameUtils::drawCards($this->game, 1);
					$handCards = unserialize($player['hand_cards']);
					$handCards = array_merge($handCards, $drawnCards);
					$player['hand_cards'] = serialize($handCards);
					$player->save();
				}
			}
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
		} elseif ($this->check == self::REVEREND_IN_THE_GAME) {
			$message = array(
				'text' => 'nemozes pouzit pivo, ked je v hre Reverend',
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