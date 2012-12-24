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
	
	protected $template = 'cards-choice.tpl';

	protected $drawType = '';
	
	protected $drawResult = NULL;

	protected function check() {
		if ($this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
			$notices = $this->actualPlayer->getNoticeList();
			if ($this->params[0] == 'barrel') {
				if ($this->interTurnReason['action'] == 'indians') {
					$this->check = self::YOU_ARE_UNDER_INDIANS_ATTACK;
				} elseif ($this->interTurnReason['action'] == 'duel') {
					$this->check = self::YOU_ARE_UNDER_DUEL_ATTACK;
				} else {
					$barrel = $this->actualPlayer->getHasBarrelOnTheTable();
					if ($barrel !== NULL) {
						if ($notices['barrel_used']) {
							$this->check = self::BARREL_ALREADY_USED;
						} else {
							$this->check = self::OK;
							$this->drawType = 'barrel';
						}
					} else {
						$this->check = self::DO_NOT_HAVE_BARREL;
					}
				}
			} elseif ($this->useCharacter && $this->actualPlayer->getCharacter()->getIsJourdonnais()) {
				if ($this->interTurnReason['action'] !== 'indians') {
					$this->params[0] = 'barrel';	// nastavime parameter ako keby chcel pouzit barel
					if ($notices['character_jourdonnais_used']) {
						$this->check = self::CHARACTER_ALREADY_USED;
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
					$this->check = self::OK;
				} elseif ($this->actualPlayer['phase'] == Player::PHASE_DYNAMITE) {
					$this->check = self::DRAW_DYNAMITE_FIRST;
				} elseif ($this->actualPlayer['phase'] == Player::PHASE_JAIL) {
					if ($this->params[0] == 'jail') {
						$card = $this->actualPlayer->getHasJailOnTheTable();
						if ($card) {
							$this->addCard($card);
							$this->check = self::OK;
						} else {
							$this->check = self::MISSING_JAIL_CARD;
						}
					} else {
						$this->check = self::DRAW_JAIL_FIRST;
					}
				} elseif ($this->actualPlayer['role'] == Role::SHERIFF && $this->game['round'] >= 2 && $this->actualPlayer['phase'] == Player::PHASE_PREDRAW) {
					$this->check = self::DRAW_EXTENSION_CARD_FIRST;
				} elseif ($this->actualPlayer['phase'] == Player::PHASE_PLAY) {
					$this->check = self::ALREADY_DRAW;
				} elseif ($this->actualPlayer['phase'] == Player::PHASE_WAITING) {
					$this->check = self::WAIT;
				} else {
					throw new Exception('neviem co sa este moze udiat', 1328469676);
				}
			} else {
				$this->check = self::NOT_YOUR_TURN;
			}
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			if ($this->params[0] == 'jail') {

				// TODO tieto karty treba najprv ukazat hracom cez log a aby sa dali vyhodit, musia byt najprv v ruke aktualneho hraca a potom ich vyhodi

				$drawnCards = GameUtils::drawCards($this->game, 1);	// TODO pocet zavisi aj od charakteru
				$isHeart = FALSE;
				$cardRepository = new CardRepository();
				$thrownCards = array();
				foreach ($drawnCards as $drawnCardId) {
					$drawnCard = $cardRepository->getOneById($drawnCardId);
					$thrownCards[] = $drawnCard;
					if ($drawnCard->getIsHeart()) {
						$isHeart = TRUE;
						// break tu nie je lebo musime prejst cez vsetky karty
						// aby sme vyrobili pole kariet ktore treba vyhodit
					}
				}
				
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
			
					// TODO next player check if is sheriff - phase predraw,
					// if has dynamite and/or jail - phase dynamite / jail, else phase draw
					if ($nextPositionPlayer->getHasDynamiteOnTheTable()) {
						$phase = Player::PHASE_DYNAMITE;
					} elseif ($nextPositionPlayer->getHasJailOnTheTable()) {
						$phase = Player::PHASE_JAIL;
					} else {
						$phase = Player::PHASE_DRAW;
					}
					$nextPositionPlayer['phase'] = $phase;
					$nextPositionPlayer->save();
				}
				// ktora karta je v odhadzovacom balicku skor? jail ci ta ktoru som potiahol?
				GameUtils::throwCards($this->game, NULL, $thrownCards);
			} elseif ($this->params[0] == 'dynamite') {

			} elseif ($this->params[0] == 'barrel') {
				$drawnCards = GameUtils::drawCards($this->game, 1);	// TODO pocet zavisi aj od charakteru
				$isHeart = FALSE;
				$cardRepository = new CardRepository();
				$thrownCards = array();
				foreach ($drawnCards as $drawnCardId) {
					$drawnCard = $cardRepository->getOneById($drawnCardId);
					$thrownCards[] = $drawnCard;
					if ($drawnCard->getIsHeart()) {
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
				$counts = $this->getCountCards();

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
		$counts = array(
			'draw' => 2,
			'pick' => 2,
			'rest_action' => '',
		);
		
		// TODO ak mame extension a je tu vlak alebo zizen tak su pocty ine
		//var_dump($this->useCharacter);exit();
		if ($this->useCharacter === TRUE) {
			$character = $this->actualPlayer->getCharacter();
			if ($character->getIsKitCarlson()) {
				$counts = array(
					'draw' => 3,
					'pick' => 2,
					'rest_action' => 'back_to_deck',
				);
			} elseif ($character->getIsPixiePete()) {
				$counts = array(
					'draw' => 3,
					'pick' => 3,
					'rest_action' => '',
				);
			} elseif ($character->getIsBillNoface()) {
				$drawAndPick = 1 + ($this->actualPlayer['max_lifes'] - $this->actualPlayer['actual_lifes']);
				$counts = array(
					'draw' => $drawAndPick,
					'pick' => $drawAndPick,
					'rest_action' => '',
				);
			} elseif ($character->getIsBlackJack()) {
				$cards = $this->game->getDrawPile();
				for ($i = 0; $i < 2; $i++) {
					$card = array_pop($cards);
				}

				$draw = 2;
				$pick = 2;
				if ($card->getIsRed()) {
					$draw = 3;
					$pick = 3;
				}

				$counts = array(
					'draw' => $draw,
					'pick' => $pick,
					'rest_action' => 'show_second',
				);
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
		} elseif ($this->check == self::DRAW_EXTENSION_CARD_FIRST) {
			$message = array(
				'toUser' => $this->loggedUser['id'],
				'localizeKey' => 'draw_extension_card_first',
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