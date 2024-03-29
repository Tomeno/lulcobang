<?php

class ThrowCommand extends Command {

	const OK = 1;

	const PLAYER_IS_NOT_IN_GAME = 2;
	
	const CANNOT_PLAY_BANG_AGAINST_DEAD_PLAYER = 3;
	
	const PLAYER_IS_TOO_FAR = 4;
	
	const YOU_HAVE_TO_THROW_TWO_CARDS = 5;
	
	const OK_DOC_HOLYDAY = 6;
	
	const TOO_MANY_LIFES = 7;
	
	const OK_SID_KETCHUM = 8;
	
	const CHARACTER_ALREADY_USED = 9;
	
	const CHARACTER_ALREADY_USED_TWICE = 10;
	
	const CANNOT_THROW_CARDS = 11;
	
	const OK_JOSE_DELGADO = 12;
	
	const OK_POKER = 13;
	
	protected $attackedPlayer = NULL;
	
	protected $template = 'you-are-attacked.tpl';
	
	protected function check() {
		if ($this->useCharacter === TRUE && $this->actualPlayer->getIsDocHolyday($this->game) && $this->actualPlayer['phase'] == Player::PHASE_PLAY) {
			$notices = $this->actualPlayer->getNoticeList();
			if (isset($notices['character_used']) && $notices['character_used'] > 0) {
				$this->check = self::CHARACTER_ALREADY_USED;
			} else {
				
				// znulujeme karty lebo uz tam jedna je z precheckera
				$this->cards = array();
				
				$firstCard = $this->actualPlayer->getCardWithId('hand', $this->params['playCardId']);
				if ($firstCard) {
					$this->addCard($firstCard);
				}

				$secondCard = $this->actualPlayer->getCardWithId('hand', $this->params['additionalCardsId']);
				if ($secondCard) {
					$this->addCard($secondCard);
				}
				
				if (count($this->cards) == 2) {
					if ($firstCard['id'] == $secondCard['id']) {
						$message = array(
							'text' => 'V sucasnosti nie je mozne pre tento charakter pouzit dve karty rovnakeho typu',
							'toUser' => $this->loggedUser['id'],
						);
						$this->addMessage($message);
					} else {
						$attackedPlayer = $this->params[0];
						if ($this->loggedUser['username'] != $attackedPlayer) {
							foreach ($this->players as $player) {
								$user = $player->getUser();
								if ($user['username'] == $attackedPlayer) {
									$this->attackedPlayer = $player;
									break;
								}
							}

							if ($this->attackedPlayer !== NULL) {
								if ($this->attackedPlayer['actual_lifes'] > 0) {
									$attackedUser = $this->attackedPlayer->getUser();
									$distance = $this->game->getDistance($this->loggedUser['username'], $attackedUser['username']);
									if ($distance <= $this->actualPlayer->getRange($this->game)) {
										$this->check = self::OK_DOC_HOLYDAY;
									} else {
										// TODO message
										$this->check = self::PLAYER_IS_TOO_FAR;
									}
								} else {
									$this->check = self::CANNOT_PLAY_BANG_AGAINST_DEAD_PLAYER;
								}
							} else {
								$this->check = self::PLAYER_IS_NOT_IN_GAME;
							}
						}
					}
				} else {
					$this->check = self::YOU_HAVE_TO_THROW_TWO_CARDS;
				}
			}	
		} elseif ($this->useCharacter === TRUE && $this->actualPlayer->getIsSidKetchum($this->game)) {
			// TODO sid ketchum moze pouzit tuto moznost aj mimo svoj tah
			
			// znulujeme karty lebo uz tam jedna je z precheckera
			$this->cards = array();
			
			$firstCard = $this->actualPlayer->getCardWithId('hand', $this->params['playCardId']);
			if ($firstCard) {
				$this->addCard($firstCard);
			}
			
			$secondCard = $this->actualPlayer->getCardWithId('hand', $this->params['additionalCardsId']);
			if ($secondCard) {
				$this->addCard($secondCard);
			}
			if (count($this->cards) == 2) {
				if ($firstCard['id'] == $secondCard['id']) {
					$message = array(
						'text' => 'V sucasnosti nie je mozne pre tento charakter pouzit dve karty rovnakeho typu',
						'toUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
				} else {
					if ($this->actualPlayer['actual_lifes'] < $this->actualPlayer['max_lifes']) {
						$this->check = self::OK_SID_KETCHUM;
					} else {
						$this->check = self::TOO_MANY_LIFES;
					}
				}
			} else {
				$this->check = self::YOU_HAVE_TO_THROW_TWO_CARDS;
			}
		} elseif ($this->useCharacter === TRUE && $this->actualPlayer->getIsJoseDelgado($this->game) && $this->actualPlayer['phase'] == Player::PHASE_PLAY) {
			$notices = $this->actualPlayer->getNoticeList();
			if (isset($notices['character_used']) && $notices['character_used'] > 1) {
				$this->check = self::CHARACTER_ALREADY_USED_TWICE;
			} else {
				$place = $this->params[1];
				if (!$place) {
					$place = 'hand';
				}
				
				if ($place == 'hand') {
					if (count($this->cards) == 1) {
						if ($this->cards[0]->getIsBlue()) {
							$this->check = self::OK_JOSE_DELGADO;
						} else {
							// karta nie je modra
						}
					} else {
						// musi vyhodit prave jednu kartu
					}
				} else {
					// musi vyhadzovat kartu z ruky
				}
			}
		} elseif ($this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
			if ($this->interTurnReason['action'] == 'poker') {
				$this->check = self::OK_POKER;
			} elseif ($this->interTurnReason['action'] == 'wild_band') {
				
				// znulujeme karty lebo uz tam jedna je z precheckera
				$this->cards = array();
				
				$firstCard = $this->actualPlayer->getCardWithId('hand', $this->params['playCardId']);
				if ($firstCard) {
					$this->addCard($firstCard);
				}

				$secondCard = $this->actualPlayer->getCardWithId('hand', $this->params['additionalCardsId']);
				if ($secondCard) {
					$this->addCard($secondCard);
				}
				
				if (count($this->cards) == 2) {
					$this->check = self::OK;
				}
				
			}
		} else {
			$handCardsCount = count($this->actualPlayer->getHandCards());
			$place = $this->params['place'];
			if (!$place) {
				$place = 'hand';
			}
			if ($this->actualPlayer['actual_lifes'] >= $handCardsCount && $place == 'hand') {
				$this->check = self::CANNOT_THROW_CARDS;
			} else {
				$this->check = self::OK;
			}
		}
	}
	
	protected function run() {
		if ($this->check == self::OK) {
			$place = $this->params['place'];
			if (!$place) {
				$place = 'hand';
			}
			// aj Vera Custer moze hrat za Garyho Lootera
			$garyLooters = array();
			foreach ($this->getPlayers() as $player) {
				// pozrieme sa na vsetkych hracov ktori este nie su mrtvi a ani nie su aktualny hrac
				if ($player['actual_lifes'] > 0 && $this->actualPlayer['id'] != $player['id']) {
					if ($player->getIsGaryLooter($this->game)) {
						$garyLooters[] = $player;
					}
				}
			}
			if ($place == 'hand' && $garyLooters) {
				// vymysliet ako davat Garymu a Vere karty na striedacku - zatial spravene len pre nahodne vybraneho Garyho
				$garyLooter = $garyLooters[array_rand($garyLooters)];
				GameUtils::moveCards($this->game, $this->cards, $this->actualPlayer, 'hand', $garyLooter, 'hand');
			} else {
				GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, $place);
			}
			
			if ($place == 'table') {
				// kedze je mozne ze rusime nejaku modru kartu ktora ovplyvnuje vzdialenost, preratame maticu
				// ak to bude velmi pomale, budeme to robit len ak je medzi vyhodenymi kartami fakt takato karta
				$matrix = GameUtils::countMatrix($this->game);
				$this->game['distance_matrix'] = serialize($matrix);
				$this->game->save();
			}
			
			if (in_array($this->interTurnReason['action'], array('wild_band'))) {
				$this->runMollyStarkAction();
				$this->changeInterturn();
			}
		} elseif ($this->check == self::OK_JOSE_DELGADO) {
			$place = 'hand';
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, $place);
			
			$drawnCards = GameUtils::drawCards($this->game, 2);
			$handCards = unserialize($this->actualPlayer['hand_cards']);
			$handCards = array_merge($handCards, $drawnCards);
			$this->actualPlayer['hand_cards'] = serialize($handCards);

			$notices = $this->actualPlayer->getNoticeList();
			if (isset($notices['character_used'])) {
				$notices['character_used'] = $notices['character_used'] + 1;
			} else {
				$notices['character_used'] = 1;
			}
			$this->actualPlayer->setNoticeList($notices);

			// zistit ci sa tu nahodou nestane to ze hracovi ostanu karty v ruke a este mu pribudnu dalsie
			$this->actualPlayer->save();
		} elseif ($this->check == self::OK_DOC_HOLYDAY) {
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			
			MySmarty::assign('card', $this->cards[0]);	// TODO mozno sem poslat nejake veci ze zautocil doc holyday svojim charakterom
			$response = MySmarty::fetch($this->template);
			$this->attackedPlayer['command_response'] = $response;
			$this->attackedPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
			$this->attackedPlayer->save();

			$notices = $this->actualPlayer->getNoticeList();
			$notices['character_used'] = 1;
			$this->actualPlayer->setNoticeList($notices);
			
			$this->actualPlayer['phase'] = Player::PHASE_WAITING;
			$this->actualPlayer->save();

			$this->game['inter_turn'] = $this->attackedPlayer['id'];
			$this->game['inter_turn_reason'] = serialize(array('action' => 'doc_holyday', 'from' => $this->actualPlayer['id'], 'to' => $this->attackedPlayer['id']));
			$this->game->save();

			
		} elseif ($this->check == self::OK_SID_KETCHUM) {
			$additionalLifes = 1;
			$newLifes = min($this->actualPlayer['actual_lifes'] + $additionalLifes, $this->actualPlayer['max_lifes']);
			$this->actualPlayer['actual_lifes'] = $newLifes;
			$this->actualPlayer->save();

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
		} elseif ($this->check == self::OK_POKER) {
			
			// odlozime karty
			$handCards = unserialize($this->actualPlayer['hand_cards']);
			$newHandCards = array();
			$selectedCard = $this->cards[0];
			foreach ($handCards as $handCard) {
				if ($handCard != $selectedCard['id']) {
					$newHandCards[] = $handCard;
				}
			}
			$this->actualPlayer['hand_cards'] = serialize($newHandCards);
			$this->actualPlayer->save();
			
			$this->interTurnReason['thrownCards'][] = $selectedCard['id'];
			
			$this->changeInterturn();
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' odhodil kartu ' . $this->cards[0]->getTitle(),
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'odhodil si kartu ' . $this->cards[0]->getTitle(),
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CHARACTER_ALREADY_USED) {
			$message = array(
				'text' => 'V tomto kole si uz pouzil svoj charakter',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CHARACTER_ALREADY_USED_TWICE) {
			$message = array(
				'text' => 'V tomto kole si uz pouzil svoj charakter dvakrat',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_THROW_CARDS) {
			$message = array(
				'text' => 'Nemozes bezdovodne odhadzovat karty',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {

	}
}

?>