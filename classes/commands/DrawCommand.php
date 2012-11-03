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

	protected $template = 'cards-choice.tpl';

	protected function check() {
		if ($this->game && $this->game['status'] == Game::GAME_STATUS_STARTED) {
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
				} else {
					throw new Exception('neviem co sa este moze udiat', 1328469676);
				}
			} else {
				$this->check = self::NOT_YOUR_TURN;
			}
		} else {
			$this->check = self::NO_GAME;
		}
	//	var_dump($this->check);
	}

	protected function run() {
		if ($this->check == self::OK) {
			if ($this->params[0] == 'jail') {

				// TODO tieto karty treba najprv ukazat hracom cez log a aby sa dali vyhodit, musia byt najprv v ruke aktualneho hraca a potom ich vyhodi

				$drawnCards = GameUtils::drawCards($this->game, 1);
				$isRed = FALSE;
				$cardRepository = new CardRepository();
				foreach ($drawnCards as $drawnCardId) {
					$drawnCard = $cardRepository->getOneById($drawnCardId);
					if ($drawnCard->getIsRed()) {
						$isRed = TRUE;
						break; // je tu break ci nie?
					}
				}
				if ($isRed) {
					$this->actualPlayer['phase'] = Player::PHASE_DRAW;
					$this->actualPlayer->save();

					GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
				} else {
					$this->actualPlayer['phase'] = Player::PHASE_NONE;
					$this->actualPlayer->save();

					GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');

					// TODO dat to priamo do triedy Game
					$nextPosition = GameUtils::getNextPosition($this->game);
					$this->game['turn'] = $nextPosition;
					$this->game->save();
			
					// TODO next player check if is sheriff - phase predraw, if has dynamite and/or jail - phase dynamite / jail, else phase draw
					foreach ($this->players as $player) {
						if ($player['position'] == $nextPosition) {
							if ($player->getHasDynamiteOnTheTable()) {
								$phase = Player::PHASE_DYNAMITE;
							} elseif ($player->getHasJailOnTheTable()) {
								$phase = Player::PHASE_JAIL;
							} else {
								$phase = Player::PHASE_DRAW;
							}
							$player['phase'] = $phase;
							$tableCards = unserialize($player['table_cards']);
							$waitCards = unserialize($player['wait_cards']);
							$player['table_cards'] = serialize(array_merge($tableCards, $waitCards));
							$player['wait_cards'] = serialize(array());
							$player->save();
							break;
						}
					}
				}
				
			} elseif ($this->params[0] == 'dynamite') {

			} else {
				$counts = $this->getCountCards();

				$drawnCards = GameUtils::drawCards($this->game, $counts['draw']);

				$possibleChoices = array(
					'drawn_cards' => $drawnCards,
					'possible_pick_count' => $counts['pick'],
					'rest_action' => $counts['rest_action'],
				);
				$this->actualPlayer['possible_choices'] = serialize($possibleChoices);
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

			} elseif ($this->params[0] == 'dynamite') {

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
		}
	}

	protected function createResponse() {
		if ($this->check == self::OK) {
			if ($this->params[0] == 'jail') {

			} elseif ($this->params[0] == 'dynamite') {

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