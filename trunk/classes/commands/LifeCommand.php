<?php

class LifeCommand extends Command {
	protected $beerCard = NULL;
	
	const OK = 1;
	
	const SAVE_LAST_LIFE = 2;
	
	const NOT_LAST_LIFE = 3;
	
	const NO_BEER_ON_HAND = 4;
	
	const ONLY_TWO_PLAYERS_IN_GAME = 5;
	
	const LOST_LIFE_AND_DRAW_CARDS = 6;
	
	const CANNOT_LOST_YOUR_LAST_LIFE = 7;
	
	const REVEREND_IN_THE_GAME = 8;
	
	const LOST_LIFE_IN_HIGH_NOON = 9;
	
	protected function check() {
		// TODO skontrolovat ci uz hrac nie je na 0
		
		$checker = new PlayerPhaseChecker($this, array('isUnderAttack'));
		if ($checker->check()) {
			if ($this->params[0] == 'beer') {
				// zistime ci hrac moze pouzit zachranne pivo
				if ($this->game->getIsHNTheReverend()) {
					$this->check = self::REVEREND_IN_THE_GAME;
				} else {
					$livePlayers = 0;
					foreach ($this->getPlayers() as $player) {
						if ($player['actual_lifes'] > 0) {
							$livePlayers++;
						}
					}

					if ($livePlayers > 2) {
						if ($this->actualPlayer['actual_lifes'] == 1) {
							$this->beerCard = $this->actualPlayer->getHasBeerOnHand();
							if ($this->beerCard) {
								$this->check = self::SAVE_LAST_LIFE;
							} else {
								$this->check = self::NO_BEER_ON_HAND;
							}
						} else {
							$this->check = self::NOT_LAST_LIFE;
						}
					} else {
						$this->check = self::ONLY_TWO_PLAYERS_IN_GAME;
					}
				}
			} else {
				// mozno bude treba pridat dalsie checkery
				$this->check = self::OK;
			}
		} elseif($this->actualPlayer['phase'] == Player::PHASE_HIGH_NOON) {
			// TODO use beer to save last life?
			$this->check = self::LOST_LIFE_IN_HIGH_NOON;
		} else {
			if ($this->useCharacter && $this->actualPlayer->getIsChuckWengam($this->game)) {
				if ($this->actualPlayer['actual_lifes'] > 1) {
					$this->check = self::LOST_LIFE_AND_DRAW_CARDS;
				} else {
					$this->check = self::CANNOT_LOST_YOUR_LAST_LIFE;
				}
			} 
		}
	}

	protected function run() {
		if ($this->check == self::SAVE_LAST_LIFE) {
			GameUtils::throwCards($this->game, $this->actualPlayer, array($this->beerCard));
			
			if ($this->actualPlayer->getIsTequilaJoe($this->game)) {
				// tequila joe si posledny zivot zachrani a 1 si este prida
				$this->actualPlayer['actual_lifes'] + 1;	
			}
			$this->runMollyStarkAction();
			$this->changeInterturn();
		} elseif ($this->check == self::LOST_LIFE_IN_HIGH_NOON) {
			// TODO save last life
			
			$newLifes = $this->actualPlayer['actual_lifes'] - 1;
			$this->actualPlayer['actual_lifes'] = $newLifes;
			$this->actualPlayer = $this->actualPlayer->save(TRUE);
			if ($newLifes <= 0) {
				$this->removePlayerFromGame();
			} else {
				$this->actualPlayer['phase'] = $this->getNextPhase($this->actualPlayer);
				$this->actualPlayer->save();
			}
		} elseif ($this->check == self::LOST_LIFE_AND_DRAW_CARDS) {
			// Chuck Wengam si moze zobrat zivot a potiahnut dve karty
			$newLifes = $this->actualPlayer['actual_lifes'] - 1;
			$handCards = unserialize($this->actualPlayer['hand_cards']);
			$drawnCards = GameUtils::drawCards($this->game, 2);
			
			foreach ($drawnCards as $drawnCard) {
				$handCards[] = $drawnCard;
			}
			
			$this->actualPlayer['actual_lifes'] = $newLifes;
			$this->actualPlayer['hand_cards'] = serialize($handCards);
			$this->actualPlayer->save();
		} elseif ($this->check == self::OK) {
			$newLifes = $this->actualPlayer['actual_lifes'] - 1;
			$notices = $this->actualPlayer->getNoticeList();
			if (isset($notices['barrel_used'])) {
				unset($notices['barrel_used']);
			}
			if (isset($notices['character_jourdonnais_used'])) {
				unset($notices['character_jourdonnais_used']);
			}
			$this->actualPlayer->setNoticeList($notices);
			$this->actualPlayer['actual_lifes'] = $newLifes;
			$this->actualPlayer = $this->actualPlayer->save(TRUE);
			if ($newLifes <= 0) {
				$this->removePlayerFromGame();
			} elseif ($this->useCharacter === TRUE && $newLifes > 0) {
				// ak bol pouzity charakter a nebol to este posledny zivot
				if ($this->actualPlayer->getIsElGringo($this->game)) {
					// el gringo
					$attackingPlayerHandCards = $this->attackingPlayer->getHandCards();
					if ($attackingPlayerHandCards) {
						$movedCards = array($attackingPlayerHandCards[array_rand($attackingPlayerHandCards)]);
						$retVal = GameUtils::moveCards($this->game, $movedCards, $this->attackingPlayer, 'hand', $this->actualPlayer, 'hand');
						$this->actualPlayer = $retVal['playerTo'];
						$this->attackingPlayer = $retVal['playerFrom'];
					}
				} elseif ($this->actualPlayer->getIsBartCassidy($this->game)) {
					// bart cassidy
					$drawnCards = GameUtils::drawCards($this->game, 1);
					$handCards = unserialize($this->actualPlayer['hand_cards']);
					foreach ($drawnCards as $drawnCard) {
						$handCards[] = $drawnCard;
					}
					$this->actualPlayer['hand_cards'] = serialize($handCards);
					$this->actualPlayer = $this->actualPlayer->save(TRUE);
				}
			}

			// TODO pocitat skore - pocet zabitych hracov
			$this->changeInterturn();
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => 'zobral si si jeden zivot',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' si zobral jeden zivot',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::SAVE_LAST_LIFE) {
			$message = array(
				'text' => 'zachranil si si posledny zivot pivom',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' si zachranil posledny zivot pivom',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NOT_LAST_LIFE) {
			$message = array(
				'text' => 'toto nie je tvoj posledny zivot, nemozes sa branit pivom',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NO_BEER_ON_HAND) {
			$message = array(
				'text' => 'nemas pivo',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::ONLY_TWO_PLAYERS_IN_GAME) {
			$message = array(
				'text' => 'nemozes pouzit pivo, hrate uz len dvaja',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_LOST_YOUR_LAST_LIFE) {
			$message = array(
				'text' => 'nemozes pouzit svoj charakter, ked mas posledny zivot',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::LOST_LIFE_AND_DRAW_CARDS) {
			$message = array(
				'text' => 'zobral si si jeden zivot a potiahol si dve karty',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' si zobral jeden zivot a potiahol si dve karty',
				'notToUser' => $this->loggedUser['id'],
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