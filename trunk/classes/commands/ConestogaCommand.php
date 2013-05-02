<?php

class ConestogaCommand extends Command {

	protected $place = 'hand';
	
	const OK = 1;

	const NO_CARDS_ON_HAND = 2;

	const NO_CARDS_ON_THE_TABLE = 3;
	
	const PLAYER_NOT_SELECTED = 4;
	
	protected function check() {
		// TODO spravit prechecker
//		$attackedPlayer = $this->params[0];
//		foreach ($this->players as $player) {
//			$user = $player->getUser();
//			if ($user['username'] == $attackedPlayer) {
//				$this->enemyPlayer = $player;
//				break;
//			}
//		}

		if ($this->attackedPlayer) {
			$place = $this->params['place'];
			
			if ($place == 'table') {
				$methods = array('hasAllCardsOnTheTableOrOnWait');
				$enemyPlayerHasCardsChecker = new EnemyPlayerHasCardsChecker($this, $methods);
				$enemyPlayerHasCardsChecker->setCards(array($this->params['enemyCardsName']));
				if ($enemyPlayerHasCardsChecker->check()) {
					$this->check = self::OK;
					$this->place = $enemyPlayerHasCardsChecker->getPlace();
				} else {
					$this->check = self::NO_CARDS_ON_THE_TABLE;
				}
			} else {
				$handCards = $this->attackedPlayer->getHandCards();
				$card = $handCards[array_rand($handCards)];
				if ($card) {
					$this->addEnemyPlayerCard($this->attackedPlayer, $card);
					$this->check = self::OK;
					$this->place = 'hand';
				} else {
					$this->check = self::NO_CARDS_ON_HAND;
				}
			}		
		} else {
			$this->check = self::PLAYER_NOT_SELECTED;
		}
	}

	protected function run() {
		if ($this->check == 1) {
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
			GameUtils::moveCards($this->game, $this->enemyPlayersCards[$this->attackedPlayer['id']], $this->attackedPlayer, 'hand', $this->actualPlayer, $this->place);
			
			if ($this->place == 'table') {
				// kedze je mozne ze berieme nejaku modru kartu ktora ovplyvnuje vzdialenost, preratame maticu
				// ak to bude velmi pomale, budeme to robit len ak je medzi zobratymi kartami fakt takato karta
				$matrix = GameUtils::countMatrix($this->game);
				$this->game['distance_matrix'] = serialize($matrix);
				$this->game->save();
			}
		}
	}

	protected function generateMessages() {
		if ($this->attackedPlayer) {
			$enemyUser = $this->attackedPlayer->getUser();
		}
		if ($this->check == self::OK) {
			// TODO doplnit v hlaske aj miesto odkial bola karta zobrata
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil Conestoga na odobratie karty ' . $enemyUser['username'],
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);

			$message = array(
				'text' => 'pouzil si Conestoga na odobratie karty ' . $enemyUser['username'],
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NO_CARDS_ON_HAND) {
			$message = array(
				'text' => $enemyUser['username'] . ' nema ziadne karty na ruke',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NO_CARDS_ON_THE_TABLE) {
			$message = array(
				'text' => $enemyUser['username'] . ' nema ziadne karty na stole',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::PLAYER_NOT_SELECTED) {
			$message = array(
				'text' => 'nevybral si ziadneho hraca',
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