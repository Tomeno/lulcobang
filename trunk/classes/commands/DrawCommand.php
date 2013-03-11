<?php

class DrawCommand extends Command {
	
	const OK = 1;

	const DRAW_EXTENSION_CARD_FIRST = 2;

	const DRAW_DYNAMITE_FIRST = 3;

	const DRAW_JAIL_FIRST = 4;

	const NO_GAME = 5;

	const NOT_YOUR_TURN = 6;

	const ALREADY_DRAW = 7;

	const MISSING_JAIL_CARD = 8;
	
	const WAIT = 9;
	
	const YOU_ARE_UNDER_ATTACK = 10;

	const DO_NOT_HAVE_BARREL = 11;
	
	const BARREL_ALREADY_USED = 12;
	
	const KO = 13;
	
	const CHARACTER_ALREADY_USED = 14;
	
	const YOU_ARE_UNDER_INDIANS_ATTACK = 15;
	
	const YOU_ARE_UNDER_DUEL_ATTACK = 16;
	
	const MISSING_DYNAMITE_CARD = 17;
	
	const PLAYER_NOT_SELECTED = 18;
	
	const PLAYER_DOESNT_HAVE_CARD_ON_THE_TABLE = 19;
	
	const NO_CARD_SELECTED = 20;
	
	const DRAW_HIGH_NOON = 21;
	
	const DRAW_FISTFUL = 22;
	
	const LOST_ONE_LIFE_AND_CONTINUE = 23;

	protected $template = 'cards-choice.tpl';

	protected $drawType = '';
	
	protected $drawResult = NULL;
	
	protected $place = 'hand';

	protected function check() {
		if ($this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
			$notices = $this->actualPlayer->getNoticeList();
			if ($this->params[0] == 'barrel') {
				if ($this->interTurnReason['action'] == 'indians') {
					$this->check = self::YOU_ARE_UNDER_INDIANS_ATTACK;
				} elseif ($this->interTurnReason['action'] == 'duel') {
					$this->check = self::YOU_ARE_UNDER_DUEL_ATTACK;
				} else {
					$barrel = $this->actualPlayer->getHasBarrelOnTheTable($this->game);
					if ($barrel !== NULL) {
						if ($notices['barrel_used']) {
							$this->check = self::BARREL_ALREADY_USED;
						} else {
							$attackingPlayerNotices = $this->attackingPlayer->getNoticeList();
							if ($attackingPlayerNotices['character_used'] && $this->attackingPlayer->getIsBelleStar($this->game)) {
								$attackingUser = $this->attackingPlayer->getUser();
								$message = array(
									'toUser' => $this->loggedUser['id'],
									'text' => 'Nemozes pouzit barel proti ' . $attackingUser['username'],
								);
								$this->addMessage($message);
							} else {
								$this->check = self::OK;
								$this->drawType = 'barrel';
							}
						}
					} else {
						$this->check = self::DO_NOT_HAVE_BARREL;
					}
				}
			} elseif ($this->useCharacter && $this->actualPlayer->getIsJourdonnais($this->game)) {
				if ($this->interTurnReason['action'] !== 'indians') {
					$this->params[0] = 'barrel';	// nastavime parameter ako keby chcel pouzit barel
					if ($notices['character_jourdonnais_used']) {
						$this->check = self::CHARACTER_ALREADY_USED;
					} elseif ($this->interTurnReason['action'] == 'duel') {
						$this->check = self::YOU_ARE_UNDER_DUEL_ATTACK;
					} else {
						$this->check = self::OK;
						$this->drawType = 'character';
					}
				} else {
					$this->check = self::YOU_ARE_UNDER_INDIANS_ATTACK;
				}
			} else {
				$this->check = self::YOU_ARE_UNDER_ATTACK;
			}
		} else {
			$playerOnTurn = $this->game->getPlayerOnTurn();
			if ($playerOnTurn['id'] == $this->actualPlayer['id']) {
				if ($this->actualPlayer['phase'] == Player::PHASE_DRAW) {
					if ($this->useCharacter === TRUE && $this->actualPlayer->getIsJesseJones($this->game)) {
						// TODO messages
						$attackedPlayer = $this->params[0];
						foreach ($this->players as $player) {
							$user = $player->getUser();
							if ($user['username'] == $attackedPlayer) {
								$this->enemyPlayer = $player;
								break;
							}
						}

						if ($this->enemyPlayer) {
							$handCards = $this->enemyPlayer->getHandCards();
							$card = $handCards[array_rand($handCards)];
							if ($card) {
								$this->addEnemyPlayerCard($this->enemyPlayer, $card);
								$this->place = 'hand';
								$this->check = self::OK;
							} else {
								$this->check = self::NO_CARDS_ON_HAND;
							}
						} else {
							$this->check = self::PLAYER_NOT_SELECTED;
						}
					} elseif ($this->useCharacter === TRUE && $this->actualPlayer->getIsPedroRamirez($this->game)) {
						// TODO skontrolovat ci su v odhadzovacom balicku nejake karty
						// TODO messages
						$this->check = self::OK;
					} elseif ($this->useCharacter === TRUE && $this->actualPlayer->getIsPatBrennan($this->game)) {
						// TODO messages
						$attackedPlayer = $this->params[0];
						foreach ($this->players as $player) {
							$user = $player->getUser();
							if ($user['username'] == $attackedPlayer) {
								$this->enemyPlayer = $player;
								break;
							}
						}

						if ($this->enemyPlayer) {
							if (isset($this->params[1])) {
								$cardName = ucfirst($this->params[1]);
								$method = 'getHas' . $cardName . 'OnTheTable';
								$card = $this->enemyPlayer->$method();
								if ($card) {
									$this->addEnemyPlayerCard($this->enemyPlayer, $card);
									$this->place = 'table';
									$this->check = self::OK;
								} else {
									$this->check = self::PLAYER_DOESNT_HAVE_CARD_ON_THE_TABLE;
								}
							} else {
								$this->check = self::NO_CARD_SELECTED;
							}
						} else {
							$this->check = self::PLAYER_NOT_SELECTED;
						}
					} elseif ($this->useCharacter === TRUE && $this->actualPlayer->getIsVeraCuster($this->game)) {
						// TODO messages
						$attackedPlayer = $this->params[0];
						foreach ($this->players as $player) {
							if ($player['actual_lifes'] > 0) {
								$user = $player->getUser();
								if ($user['username'] == $attackedPlayer) {
									$this->enemyPlayer = $player;
									break;
								}
							}
						}

						if ($this->enemyPlayer) {
							$this->check = self::OK;
						} else {
							// todo message o tom ze vera chce pouzit charakter ale nevybrala ziadneho hraca
						}
					} else {
						$this->check = self::OK;
					}
				} elseif ($this->actualPlayer['phase'] == Player::PHASE_DYNAMITE) {
					if ($this->params[0] == 'dynamite') {
						$card = $this->actualPlayer->getHasDynamiteOnTheTable($this->game);
						if ($card) {
							$this->addCard($card);
							$this->check = self::OK;
						} else {
							$this->check = self::MISSING_DYNAMITE_CARD;
						}
					} else {
						$this->check = self::DRAW_DYNAMITE_FIRST;
					}
				} elseif ($this->actualPlayer['phase'] == Player::PHASE_JAIL) {
					if ($this->params[0] == 'jail') {
						$card = $this->actualPlayer->getHasJailOnTheTable($this->game);
						if ($card) {
							$this->addCard($card);
							$this->check = self::OK;
						} else {
							$this->check = self::MISSING_JAIL_CARD;
						}
					} else {
						$this->check = self::DRAW_JAIL_FIRST;
					}
				} elseif ($this->actualPlayer['phase'] == Player::PHASE_PLAY) {
					$this->check = self::ALREADY_DRAW;
				} elseif ($this->actualPlayer['phase'] == Player::PHASE_WAITING) {
					$this->check = self::WAIT;
				} elseif ($this->actualPlayer['phase'] == Player::PHASE_DRAW_HIGH_NOON) {
					$this->check = self::DRAW_HIGH_NOON;
				} elseif ($this->actualPlayer['phase'] == Player::PHASE_DRAW_FISTFUL) {
					$this->check = self::DRAW_FISTFUL;
				} elseif ($this->actualPlayer['phase'] == Player::PHASE_HIGH_NOON) {
					$this->check = self::LOST_ONE_LIFE_AND_CONTINUE;
				} else {
					throw new Exception('neviem co sa este moze udiat', 1328469676);
				}
			} else {
				$this->check = self::NOT_YOUR_TURN;
			}
		}
		var_dump($this->check);
	}

	protected function run() {
		if ($this->check == self::OK) {
			if ($this->params[0] == 'jail') {

				// TODO tieto karty treba najprv ukazat hracom cez log a aby sa dali vyhodit, musia byt najprv v ruke aktualneho hraca a potom ich vyhodi

				$count = 1;
				if ($this->useCharacter && $this->actualPlayer->getIsLuckyDuke($this->game)) {
					$count = 2;
				}
				
				$drawnCards = GameUtils::drawCards($this->game, $count);	// TODO pocet zavisi aj od charakteru
				$isHeart = FALSE;
				$cardRepository = new CardRepository();
				$thrownCards = array();
				foreach ($drawnCards as $drawnCardId) {
					$drawnCard = $cardRepository->getOneById($drawnCardId);
					$thrownCards[] = $drawnCard;
					if ($drawnCard->getIsHearts($this->game)) {
						$isHeart = TRUE;
						// break tu nie je lebo musime prejst cez vsetky karty
						// aby sme vyrobili pole kariet ktore treba vyhodit
					}
				}
				GameUtils::throwCards($this->game, NULL, $thrownCards);
				
				if ($isHeart) {
					$this->drawResult = self::OK;
					$this->actualPlayer['phase'] = Player::PHASE_DRAW;
					$this->actualPlayer->save();

					GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
				} else {
					$this->drawResult = self::KO;
					$this->actualPlayer['phase'] = Player::PHASE_NONE;
					$this->actualPlayer->save();

					GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');

					$nextPositionPlayer = GameUtils::getPlayerOnNextPosition($this->game, $this->actualPlayer);
					$this->game['turn'] = $nextPositionPlayer['id'];
					$this->game->save();
			
					$nextPositionPlayer['phase'] = $this->getNextPhase($nextPositionPlayer);
					$nextPositionPlayer->save();
				}
				
			} elseif ($this->params[0] == 'dynamite') {
				$count = 1;
				if ($this->useCharacter && $this->actualPlayer->getIsLuckyDuke($this->game)) {
					$count = 2;
				}
				
				// TODO skontrolovat ci funguje prekliatie
				$drawnCards = GameUtils::drawCards($this->game, $count);
				$isSafe = FALSE;
				$cardRepository = new CardRepository();
				$thrownCards = array();
				foreach ($drawnCards as $drawnCardId) {
					$drawnCard = $cardRepository->getOneById($drawnCardId);
					$thrownCards[] = $drawnCard;
					if (!$drawnCard->getIsSpades($this->game) || in_array($drawnCard['value'], array('10', 'J', 'Q', 'K', 'A'))) {
						$isSafe = TRUE;
						// break tu nie je lebo musime prejst cez vsetky karty
						// aby sme vyrobili pole kariet ktore treba vyhodit
					}
				}

				$dynamite = $this->actualPlayer->getHasDynamiteOnTheTable($this->game);
				if ($isSafe === TRUE) {
					$this->drawResult = self::OK;
					if ($this->actualPlayer->getHasJailOnTheTable($this->game)) {
						$phase = Player::PHASE_JAIL;
					} else {
						$phase = Player::PHASE_DRAW;
					}
					$this->actualPlayer['phase'] = $phase;
					$this->actualPlayer = $this->actualPlayer->save(TRUE);
					
					// posunieme dynamit dalsiemu hracovi
					$nextPositionPlayer = GameUtils::getPlayerOnNextPosition($this->game, $this->actualPlayer);
					if ($nextPositionPlayer->getHasDynamiteOnTheTable($this->game)) {
						// ak dalsi hrac uz ma na stole dynamit, musim sa pozriet na hraca za nim
						$nextPositionPlayer = GameUtils::getPlayerOnNextPosition($this->game, $nextPositionPlayer);
					}
					if ($nextPositionPlayer['id'] != $this->actualPlayer['id']) {
						// v pripade dvoch hracov a dvoch dynamitov sa dynamity neposuvaju
						GameUtils::moveCards($this->game, array($dynamite), $this->actualPlayer, 'table', $nextPositionPlayer, 'table');
					}
				} else {
					$this->drawResult = self::KO;
					
					// stiahneme hracovi tri zivoty
					$newLifes = $this->actualPlayer['actual_lifes'] - 3;
					$this->actualPlayer['actual_lifes'] = $newLifes;
					if ($this->actualPlayer->getHasJailOnTheTable($this->game)) {
						$phase = Player::PHASE_JAIL;
					} else {
						$phase = Player::PHASE_DRAW;
					}
					$this->actualPlayer['phase'] = $phase;
					$this->actualPlayer = $this->actualPlayer->save(TRUE);
					
					GameUtils::throwCards($this->game, NULL, $thrownCards);
					
					// zahodime dynamit
					$retVal = GameUtils::throwCards($this->game, $this->actualPlayer, array($dynamite), 'table');
					$this->actualPlayer = $retVal['player'];
					$this->game = $retVal['game'];
					
					if ($newLifes <= 0) {
						// TODO check ci nema na ruke pivo / piva a ak ano automaticky ich pouzit na zachranu
						// aspon dokym nebude na 1 zivote
						// nezabudnut na tequilla joe
						
						$nextPositionPlayer = GameUtils::getPlayerOnNextPosition($this->game, $this->actualPlayer);
						$this->game['turn'] = $nextPositionPlayer['id'];
						$this->game->save();
						
						// TODO next player check if is sheriff - phase predraw,
						// if has dynamite and/or jail - phase dynamite / jail, else phase draw
						if ($nextPositionPlayer->getHasDynamiteOnTheTable($this->game)) {
							$phase = Player::PHASE_DYNAMITE;
						} elseif ($nextPositionPlayer->getHasJailOnTheTable($this->game)) {
							$phase = Player::PHASE_JAIL;
						} else {
							$phase = Player::PHASE_DRAW;
						}
						$nextPositionPlayer['phase'] = $phase;
						$nextPositionPlayer->save();

						$this->removePlayerFromGame();
					}
				}
				
			} elseif ($this->params[0] == 'barrel') {
				$count = 1;
				if ($this->useCharacter && $this->actualPlayer->getIsLuckyDuke($this->game)) {
					$count = 2;
				}
				$drawnCards = GameUtils::drawCards($this->game, $count);	// TODO pocet zavisi aj od charakteru
				$isHeart = FALSE;
				$cardRepository = new CardRepository();
				$thrownCards = array();
				foreach ($drawnCards as $drawnCardId) {
					$drawnCard = $cardRepository->getOneById($drawnCardId);
					$thrownCards[] = $drawnCard;
					if ($drawnCard->getIsHearts($this->game)) {
						$isHeart = TRUE;
						// break tu nie je lebo musime prejst cez vsetky karty
						// aby sme vyrobili pole kariet ktore treba vyhodit
					}
				}
				
				$notices = $this->actualPlayer->getNoticeList();
				if ($this->drawType == 'barrel') {
					$notices['barrel_used'] = 1;
				} elseif ($this->drawType == 'character') {
					$notices['character_jourdonnais_used'] = 1;
				}
				$this->actualPlayer->setNoticeList($notices);
				$this->actualPlayer = $this->actualPlayer->save(TRUE);
				
				if ($isHeart) {
					$this->drawResult = self::OK;
					
					$this->changeInterturn();
				} else {
					$this->drawResult = self::KO;
				}
				GameUtils::throwCards($this->game, NULL, $thrownCards);
			} else {
				if ($this->useCharacter === TRUE && $this->actualPlayer->getIsVeraCuster($this->game)) {
					$notices = $this->actualPlayer->getNoticeList();
					$selectedCharacter = $this->enemyPlayer->getCharacter();
					$notices['selected_character'] = $selectedCharacter['id'];
					$this->actualPlayer->setNoticeList($notices);
					$this->actualPlayer = $this->actualPlayer->save(TRUE);
					
					// kedze je mozne ze si vyberieme nejaky charakter, ktory ovplyvnuje vzdialenost, preratame maticu
					$matrix = GameUtils::countMatrix($this->game);
					$this->game['distance_matrix'] = serialize($matrix);
					$this->game = $this->game->save(TRUE);
				}
				$counts = $this->getCountCards();
				
				if ($this->useCharacter === TRUE) {
					if ($this->actualPlayer->getIsJesseJones($this->game)) {
						$retVal = GameUtils::moveCards($this->game, $this->enemyPlayersCards[$this->enemyPlayer['id']], $this->enemyPlayer, 'hand', $this->actualPlayer, $this->place);
						$this->actualPlayer = $retVal['playerTo'];
						$this->game = $retVal['game'];
					} elseif ($this->actualPlayer->getIsPatBrennan($this->game)) {
						$retVal = GameUtils::moveCards($this->game, $this->enemyPlayersCards[$this->enemyPlayer['id']], $this->enemyPlayer, 'hand', $this->actualPlayer, $this->place);
						$this->actualPlayer = $retVal['playerTo'];
						$this->game = $retVal['game'];
						
						// kedze je mozne ze rusime nejaku modru kartu ktora ovplyvnuje vzdialenost, preratame maticu
						// ak to bude velmi pomale, budeme to robit len ak je medzi zrusenymi kartami fakt takato karta
						$matrix = GameUtils::countMatrix($this->game);
						$this->game['distance_matrix'] = serialize($matrix);
						$this->game->save();
						
					} elseif ($this->actualPlayer->getIsPedroRamirez($this->game)) {
						$throwPile = unserialize($this->game['throw_pile']);
						$card = array_pop($throwPile);
						$this->game['throw_pile'] = serialize($throwPile);
						$handCards = unserialize($this->actualPlayer['hand_cards']);
						$handCards[] = $card;
						$this->actualPlayer['hand_cards'] = serialize($handCards);
					}
				}
				
				$drawnCards = GameUtils::drawCards($this->game, $counts['draw']);

				$possibleChoices = array(
					'drawn_cards' => $drawnCards,
					'possible_pick_count' => $counts['pick'],
					'rest_action' => $counts['rest_action'],
				);
				$this->actualPlayer['possible_choices'] = serialize($possibleChoices);
				$this->actualPlayer['phase'] = Player::PHASE_PLAY;
				$this->actualPlayer->save();
			}
		}
	}

	private function getCountCards() {
		// default
		if ($this->game->getIsHNTheTrain() || ($this->game->getIsHNGhostTown() && $this->actualPlayer->getIsGhost())) {
			$counts = array(
				'draw' => 3,
				'pick' => 3,
				'rest_action' => '',
			);
		} elseif ($this->game->getIsHNThirst()) {
			$counts = array(
				'draw' => 1,
				'pick' => 1,
				'rest_action' => '',
			);
		} else {
			$counts = array(
				'draw' => 2,
				'pick' => 2,
				'rest_action' => '',
			);
		}

		// TODO ak mame extension a je tu vlak alebo zizen tak su pocty ine
		if ($this->useCharacter === TRUE) {
			$character = $this->actualPlayer;
			if ($character->getIsKitCarlson($this->game)) {
				$counts = array(
					'draw' => 3,
					'pick' => 2,
					'rest_action' => 'back_to_deck',
				);
				if ($this->game->getIsHNTheTrain()) {
					$counts['pick'] = 3;
					$counts['rest_action'] = '';
				} elseif ($this->game->getIsHNThirst()) {
					$counts = array(
						'draw' => 3,
						'pick' => 1,
						'rest_action' => 'back_to_deck',
					);
				}
			} elseif ($character->getIsPixiePete($this->game)) {
				$counts = array(
					'draw' => 3,
					'pick' => 3,
					'rest_action' => '',
				);
				if ($this->game->getIsHNTheTrain()) {
					$counts['draw'] = $counts['draw'] + 1;
					$counts['pick'] = $counts['pick'] + 1;
				} elseif ($this->game->getIsHNThirst()) {
					$counts['draw'] = $counts['draw'] - 1;
					$counts['pick'] = $counts['pick'] - 1;
				}
			} elseif ($character->getIsBillNoface($this->game)) {
				$drawAndPick = 1 + ($this->actualPlayer['max_lifes'] - $this->actualPlayer['actual_lifes']);
				$counts = array(
					'draw' => $drawAndPick,
					'pick' => $drawAndPick,
					'rest_action' => '',
				);
				if ($this->game->getIsHNTheTrain()) {
					$counts['draw'] = $counts['draw'] + 1;
					$counts['pick'] = $counts['pick'] + 1;
				} elseif ($this->game->getIsHNThirst()) {
					$counts['draw'] = $counts['draw'] - 1;
					$counts['pick'] = $counts['pick'] - 1;
				}
			} elseif ($character->getIsBlackJack($this->game)) {
				if ($this->game->getIsHNThirst()) {
					$counts = array(
						'draw' => 1,
						'pick' => 1,
					);
				} else {
					$cards = $this->game->getDrawPile();
					for ($i = 0; $i < 2; $i++) {
						$card = array_pop($cards);
					}
					// TODO show card
					$draw = 2;
					$pick = 2;
					if ($card->getIsRed($this->game)) {
						$draw = 3;
						$pick = 3;
					}

					$counts = array(
						'draw' => $draw,
						'pick' => $pick,
						'rest_action' => 'show_second',
					);
					if ($this->game->getIsHNTheTrain()) {
						$counts['draw'] = $counts['draw'] + 1;
						$counts['pick'] = $counts['pick'] + 1;
					}
				}
			} elseif ($character->getIsJesseJones($this->game)) {
				$counts = array(
					'draw' => 1,
					'pick' => 1,
					'rest_action' => '',
				);
				if ($this->game->getIsHNTheTrain()) {
					$counts['draw'] = $counts['draw'] + 1;
					$counts['pick'] = $counts['pick'] + 1;
				} elseif ($this->game->getIsHNThirst()) {
					$counts['draw'] = $counts['draw'] - 1;
					$counts['pick'] = $counts['pick'] - 1;
				}
			} elseif ($character->getIsPedroRamirez($this->game)) {
				$counts = array(
					'draw' => 1,
					'pick' => 1,
					'rest_action' => '',
				);
				if ($this->game->getIsHNTheTrain()) {
					$counts['draw'] = $counts['draw'] + 1;
					$counts['pick'] = $counts['pick'] + 1;
				} elseif ($this->game->getIsHNThirst()) {
					$counts['draw'] = $counts['draw'] - 1;
					$counts['pick'] = $counts['pick'] - 1;
				}
			} elseif ($character->getIsPatBrennan($this->game)) {
				$counts = array(
					'draw' => 0,
					'pick' => 0,
					'rest_action' => '',
				);
				if ($this->game->getIsHNTheTrain()) {
					$counts['draw'] = $counts['draw'] + 1;
					$counts['pick'] = $counts['pick'] + 1;
				} elseif ($this->game->getIsHNThirst()) {
					$counts['draw'] = 0;	// nie zeby to defaultne nemal takto ale aby sa na to nezabudlo :)
					$counts['pick'] = 0;
				}
			}
		}
		return $counts;
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			if ($this->params[0] == 'jail') {
				if ($this->drawResult == self::OK) {
					$message = array(
						'text' => 'usiel si z vazenia',
						'toUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
					
					$message = array(
						'text' => $this->loggedUser['username'] . ' usiel z vazenia',
						'notToUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
				} elseif ($this->check == self::MISSING_JAIL_CARD) {
					$message = array(
						'text' => 'nie si vo vazeni',
						'toUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
				} else {
					$message = array(
						'text' => 'nepodarilo sa ti ujst z vazenia',
						'toUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
					
					$message = array(
						'text' => $this->loggedUser['username'] . ' ostava vo vazeni',
						'notToUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
				}
			} elseif ($this->params[0] == 'dynamite') {
				if ($this->drawResult == self::OK) {
					$message = array(
						'text' => 'dynamit ti nevybuchol',
						'toUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
					
					$message = array(
						'text' => $this->loggedUser['username'] . ' sa vyhol vybuchu dynamitu',
						'notToUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
				} elseif ($this->check == self::MISSING_DYNAMITE_CARD) {
					$message = array(
						'text' => 'nemas pred sebou dynamit',
						'toUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
				} else {
					$message = array(
						'text' => 'dynamit ti vybuchol v rukach',
						'toUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
					
					$message = array(
						'text' => $this->loggedUser['username'] . 'vi vybuchol dynamit v rukach',
						'notToUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
				}
			} elseif ($this->params[0] == 'barrel') {
				
				// TODO lokalizovane hlasky nech beru do uvahy aj to ze ci bol pouzity barel alebo charakter
				// mame to v drawType
				
				if ($this->drawResult == self::OK) {
					$message = array(
						'text' => 'zachranil ta barel',
						'toUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
					
					$message = array(
						'text' => $this->loggedUser['username'] . ' bol zachraneny barelom',
						'notToUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
				} else {
					$message = array(
						'text' => 'barel ta nezachranil, musis pouzit kartu vedla alebo si stiahnut zivot',
						'toUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
					
					$message = array(
						'text' => $this->loggedUser['username'] . ' nebol zachraneny barelom, musi pouzit kartu vedla alebo si stiahnut zivot',
						'notToUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
				}
			} else {
				$message = array(
					'notToUser' => $this->loggedUser['id'],
					'localizeKey' => 'player_draw_cards',
					'localizeParams' => array($this->loggedUser['username']),
				);
				$this->addMessage($message);

				$message = array(
					'toUser' => $this->loggedUser['id'],
					'localizeKey' => 'you_draw_cards',
				);
				$this->addMessage($message);
			}
		} elseif ($this->check == self::DRAW_HIGH_NOON) {
			$message = array(
				'toUser' => $this->loggedUser['id'],
				'text' => 'Potiahni najprv kartu z balicka High noon',
			);
			$this->addMessage($message);
		} elseif ($this->check == self::DRAW_FISTFUL) {
			$message = array(
				'toUser' => $this->loggedUser['id'],
				'text' => 'Potiahni najprv kartu z balicka Fistful of cards',
			);
			$this->addMessage($message);
		} elseif ($this->check == self::DRAW_DYNAMITE_FIRST) {
			$message = array(
				'toUser' => $this->loggedUser['id'],
				'localizeKey' => 'draw_dynamite_first',
			);
			$this->addMessage($message);
		} elseif ($this->check == self::DRAW_JAIL_FIRST) {
			$message = array(
				'toUser' => $this->loggedUser['id'],
				'localizeKey' => 'draw_jail_first',
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NOT_YOUR_TURN) {
			$message = array(
				'toUser' => $this->loggedUser['id'],
				'localizeKey' => 'not_your_turn',
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NO_GAME) {
			$message = array(
				'toUser' => $this->loggedUser['id'],
				'localizeKey' => 'cannot_draw_no_game_in_room',
			);
			$this->addMessage($message);
		} elseif ($this->check == self::ALREADY_DRAW) {
			$message = array(
				'toUser' => $this->loggedUser['id'],
				'localizeKey' => 'you_have_already_draw',
			);
			$this->addMessage($message);
		} elseif ($this->check == self::WAIT) {
			$message = array(
				'toUser' => $this->loggedUser['id'],
				'localizeKey' => 'you_have_to_wait',
			);
			$this->addMessage($message);
		} elseif ($this->check == self::YOU_ARE_UNDER_ATTACK) {
			$message = array(
				'toUser' => $this->loggedUser['id'],
				'localizeKey' => 'you_are_under_attack_use_defensive_cards',
			);
			$this->addMessage($message);
		} elseif ($this->check == self::DO_NOT_HAVE_BARREL) {
			$message = array(
				'text' => 'nemas barel',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::BARREL_ALREADY_USED) {
			$message = array(
				'text' => 'na tento utok si uz pouzil barel',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CHARACTER_ALREADY_USED) {
			$message = array(
				'text' => 'na tento utok si uz pouzil svoj charakter',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::YOU_ARE_UNDER_INDIANS_ATTACK) {
			$message = array(
				'text' => 'Proti utoku indianov nemozes pouzit barel',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::YOU_ARE_UNDER_DUEL_ATTACK) {
			$message = array(
				'text' => 'Pri duele nemozes pouzit barel',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::LOST_ONE_LIFE_AND_CONTINUE) {
			$message = array(
				'text' => 'Stiahni si jeden zivot a pokracuj v hre',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
		
	}

	protected function createResponse() {
		if ($this->check == self::OK) {
			if ($this->params[0] == 'jail') {

			} elseif ($this->params[0] == 'dynamite') {
			
			} elseif ($this->params[0] == 'barrel') {

			} else {
				$possibleChoices = unserialize($this->actualPlayer['possible_choices']);
				$cardRepository = new CardRepository();

				$possibleCards = array();

				foreach ($possibleChoices['drawn_cards'] as $cardId) {
					$possibleCards[] = $cardRepository->getOneById($cardId);
				}

				MySmarty::assign('possiblePickCount', $possibleChoices['possible_pick_count']);
				MySmarty::assign('possibleCards', $possibleCards);
				MySmarty::assign('possibleCardsCount', count($possibleCards));
				MySmarty::assign('game', $this->game);
				$response = MySmarty::fetch($this->template);

				$this->actualPlayer['command_response'] = $response;
				$this->actualPlayer->save();

				return $response;
			}
		}
	}
}

?>