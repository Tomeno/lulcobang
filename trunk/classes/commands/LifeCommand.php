<?php

class LifeCommand extends Command {
	
	protected $beerCard = NULL;
	
	protected $shootgunCards = array();
		
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
			if ($this->actualPlayer['actual_lifes'] == 1 && $this->params['playCardName'] == 'beer') {
				// TODO save last life + shootgun
				
				// zistime ci hrac moze pouzit zachranne pivo
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
						if ($this->actualPlayer['actual_lifes'] == 1) {
							$this->beerCard = $this->actualPlayer->getHasBeerOnHand();
							if ($this->beerCard) {
								if ($this->interTurnReason['action'] == 'bang' && $this->attackingPlayer->getHasShootgunOnTheTable()) {
									// TODO - mozno hrac nema karty na ruke
									if ($this->params['additionalCardsId']) {
										$card = $this->actualPlayer->getCardWithId('hand', $this->params['additionalCardsId']);
										if ($card) {
											$this->shootgunCards[] = $card;
											$this->check = self::SAVE_LAST_LIFE;
										} else {
											// nemas kartu s id playcardid
										}
									} else {
										// TODO musis vyhodit este jednu kartu
									}
								} else {
									$this->check = self::SAVE_LAST_LIFE;
								}
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
				if ($this->interTurnReason['action'] == 'bang' && $this->attackingPlayer->getHasShootgunOnTheTable()) {
					if (count($this->actualPlayer->getHandCards())) {
						// TODO - mozno hrac nema karty na ruke
						if ($this->params['playCardId']) {
							$card = $this->actualPlayer->getCardWithId('hand', $this->params['playCardId']);
							if ($card) {
								$this->shootgunCards[] = $card;
								$this->check = self::OK;
							} else {
								// nemas kartu s id playcardid
							}
						} else {
							// TODO musis vyhodit este jednu kartu
						}
					} else {
						$this->check = self::OK;
					}
				} else {
					// mozno bude treba pridat dalsie checkery
					$this->check = self::OK;
				}
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
			
			$this->drawBountyCard();
			
			// za normalnych okolnosti -1 zivot
			$removedLifes = 1;
			if ($this->interTurnReason['action'] == 'aiming') {
				// dvojita rana - 2 zivoty
				$removedLifes += 1;
			}
			
			if ($this->attackingPlayer->getHasShootgunOnTheTable() && $this->shootgunCards) {
				GameUtils::throwCards($this->game, $this->actualPlayer, $this->shootgunCards);
			}
			
			// pivo +1 zivot
			$removedLifes -= 1;
			if ($this->actualPlayer->getIsTequilaJoe($this->game)) {
				// tequila joe si posledny zivot zachrani a 1 si este prida
				$removedLifes -= 1;
			}
			$this->actualPlayer['actual_lifes'] - $removedLifes;
			
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
			$this->drawBountyCard();
			
			$removedLifes = 1;
			if ($this->interTurnReason['action'] == 'aiming') {
				$removedLifes = 2;
			}
			
			if ($this->attackingPlayer->getHasShootgunOnTheTable() && $this->shootgunCards) {
				GameUtils::throwCards($this->game, $this->actualPlayer, $this->shootgunCards);
			}
			
			$newLifes = $this->actualPlayer['actual_lifes'] - $removedLifes;
			
			// ak by mal teren kill zomriet taha kartu
			if ($newLifes < 1 && $this->actualPlayer->getIsTerenKill()) {
				$drawnCards = GameUtils::drawCards($this->game, 1);
				$cardRepository = new CardRepository(TRUE);
				$drawnCard = $cardRepository->getOneById($drawnCards[0]);
				if (!$drawnCard->getIsSpades($this->game)) {
					$newLifes = 1;
					
					// potiahne si este 1 kartu
					$drawnCards = GameUtils::drawCards($this->game, 1);
					$handCards = unserialize($this->actualPlayer['hand_cards']);
					$handCards = array_merge($handCards, $drawnCards);
					$this->actualPlayer['hand_cards'] = serialize($handCards);
				}
			}
			
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
			} else { //if ($this->useCharacter === TRUE && $newLifes > 0) { // ak bol pouzity charakter a nebol to este posledny zivot
				// el gringo a bart cassidy si tahaju karty
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

	protected function drawBountyCard() {
		if ($this->actualPlayer->getHasBountyOnTheTable()) {
			$drawnCards = GameUtils::drawCards($this->game, 1);
			$handCards = unserialize($this->attackingPlayer['hand_cards']);
			$handCards = array_merge($handCards, $drawnCards);
			$this->attackingPlayer['hand_cards'] = serialize($handCards);
		}
	}
	
	protected function generateMessages() {
		// todo pocet zivotov ktore si hrac zobral
		
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