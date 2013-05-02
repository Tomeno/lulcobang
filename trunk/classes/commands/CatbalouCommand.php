<?php

class CatbalouCommand extends Command {

	protected $place = 'hand';

	const OK = 1;
	
	const NO_CARDS_ON_HAND = 2;

	const NO_CARDS_ON_THE_TABLE = 3;
	
	const PLAYER_NOT_SELECTED = 4;
	
	const CANNOT_ATTACK_APACHE_KID = 5;
	
	protected function check() {
		// TODO spravit prechecker
//		$attackedPlayer = $this->params['enemyPlayerUsername'];
//		foreach ($this->players as $player) {
//			$user = $player->getUser();
//			if ($user['username'] == $attackedPlayer) {
//				$this->attackedPlayer = $player;
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
				if ($this->params['enemyCardsName']) {
					// TODO asi radsej cez idecko lebo ak ma viac rovnakych zobral by som nieco co nechcem
					$method = 'getHas' . ucfirst($this->params['enemyCardsName']) . 'OnHand';
					$card = $this->attackedPlayer->$method($this->game);
					if ($card) {
						$this->addEnemyPlayerCard($this->attackedPlayer, $card);
						$this->check = self::OK;
						$this->place = 'hand';
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
			}
		} else {
			$this->check = self::PLAYER_NOT_SELECTED;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			$canAttack = $this->checkCanAttackApacheKid();
				
			if ($canAttack === TRUE) {
				GameUtils::throwCards($this->game, $this->attackedPlayer, $this->enemyPlayersCards[$this->attackedPlayer['id']], $this->place);

				if ($this->place == 'table') {
					// kedze je mozne ze rusime nejaku modru kartu ktora ovplyvnuje vzdialenost, preratame maticu
					// ak to bude velmi pomale, budeme to robit len ak je medzi zrusenymi kartami fakt takato karta
					$matrix = GameUtils::countMatrix($this->game);
					$this->game['distance_matrix'] = serialize($matrix);
					$this->game->save();
				}
			} else {
				$this->check = self::CANNOT_ATTACK_APACHE_KID;
			}
		}
	}

	protected function generateMessages() {
		if ($this->attackedPlayer) {
			$enemyUser = $this->attackedPlayer->getUser();
		}
		print_r($this->check);
		if ($this->check == self::OK) {
			// TODO doplnit v hlaske aj miesto odkial bola karta zobrata
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil cat balou na odobratie karty ' . $enemyUser['username'],
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);

			$message = array(
				'text' => 'pouzil si catbalou na odobratie karty ' . $enemyUser['username'],
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
		} elseif ($this->check == self::CANNOT_ATTACK_APACHE_KID) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil cat balou na odobratie karty ' . $enemyUser['username'],
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);

			$message = array(
				'text' => 'pouzil si catbalou na odobratie karty ' . $enemyUser['username'],
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'Utok karovymi kartami proti Apache Kidovi nema ziadny efekt',
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
		return '';
	}
}
?>