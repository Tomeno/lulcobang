<?php

class BangCommand extends Command {
	const OK = 1;
	
	const DO_NOT_HAVE_BANG = 2;
	
	const NOT_YOUR_TURN = 3;
	
	const CANNOT_PLAY_BANG = 4;
	
	const CANNOT_PLAY_BANG_AGAINST_YOURSELF = 5;
	
	const PLAYER_IS_NOT_IN_GAME = 6;
	
	const CANNOT_PLAY_BANG_AGAINST_DEAD_PLAYER = 7;
	
	const PLAYER_IS_TOO_FAR = 8;
	
	const USED_BANG_ALREADY = 9;
	
	protected $bangCard = NULL;

	protected $attackedPlayer = NULL;

	protected $template = 'you-are-attacked.tpl';
	
	protected $duelBangTemplate = 'player-plays-bang.tpl';
	
	protected $duelAttackedUser = NULL;

	protected function check() {
		if ($this->actualPlayer['phase'] == Player::PHASE_PLAY) {
			$canPlayMoreBangs = FALSE;
			if ($this->actualPlayer['bang_used'] < 1) {
				$canPlayMoreBangs = TRUE;
				// TODO pocet kariet bang zavisi od kariet v rozsireni kazatel a prestrelka, upravit tento if
				// ani willy kid nemoze strielat ked pride kazatel
			} elseif ($this->useCharacter === TRUE && $this->actualPlayer->getIsWillyTheKid()) {
				$canPlayMoreBangs = TRUE;
			} elseif ($this->actualPlayer->getHasVolcanicOnTheTable()) {
				$canPlayMoreBangs = TRUE;
			}
			
			if ($canPlayMoreBangs) {
				// TODO spravit k tomuto nejaku metodu v commande lebo sa to pouziva dost casto
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
							$this->bangCard = $this->cards[0];
							if ($this->bangCard !== NULL) {
								$attackedUser = $this->attackedPlayer->getUser();
								$distance = $this->game->getDistance($this->loggedUser['username'], $attackedUser['username']);
								if ($distance <= $this->actualPlayer->getRange()) {
									$this->check = self::OK;
								} else {
									$this->check = self::PLAYER_IS_TOO_FAR;
								}
							} else {
								$this->check = self::DO_NOT_HAVE_BANG;
							}
						} else {
							$this->check = self::CANNOT_PLAY_BANG_AGAINST_DEAD_PLAYER;
						}
					} else {
						$this->check = self::PLAYER_IS_NOT_IN_GAME;
					}
				} else {
					$this->check = self::CANNOT_PLAY_BANG_AGAINST_YOURSELF;
				}
			} else {
				$this->check = self::USED_BANG_ALREADY;
			}
		} elseif ($this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
			if ($this->interTurnReason['action'] == 'indians') {
				$this->check = self::OK;
			} elseif ($this->interTurnReason['action'] == 'duel') {
				$this->check = self::OK;
			} else {
				$this->check = self::CANNOT_PLAY_BANG;
			}
		} elseif ($this->actualPlayer['phase'] == Player::PHASE_DRAW) {
			$message = array(
				'localizeKey' => 'draw_cards_first',
			);
			$this->addMessage($message);
		} else {
			$this->check = self::NOT_YOUR_TURN;
		}
		// TODO check if actual player is in play state - momentalne moze hrat bang aj ked este nepotiahol jail a myslim ze by to slo aj keby nepotiahol vobec

		// TODO check actual player state - if waiting cannot play bang again

		// TODO check if has volcanic or is willy the kid for playing more than one bang in a round

		// TODO create as checker
			
	}

	protected function run() {
		if ($this->check == self::OK) {
			if ($this->interTurnReason['action'] == 'indians') {
				$this->runMollyStarkAction();
				$this->changeInterturn();
			} elseif ($this->interTurnReason['action'] == 'duel') {
				$attackedPlayerId = intval($this->interTurnReason['to']);
				$playerRepository = new PlayerRepository();
				$this->attackedPlayer = $playerRepository->getOneById($attackedPlayerId);
				
				MySmarty::assign('card', $this->cards[0]);
				$response = MySmarty::fetch($this->duelBangTemplate);
				
				if ($this->attackingPlayer['id'] == $this->actualPlayer['id']) {
					$this->attackedPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
					$this->attackedPlayer['command_response'] = $response;
					$this->attackedPlayer->save();
					
					$this->duelAttackedUser = $this->attackedPlayer->getUser();
					
					$this->game['inter_turn'] = $this->attackedPlayer['id'];
					$this->game->save();
				} elseif ($this->attackedPlayer['id'] == $this->actualPlayer['id']) {
					$this->attackingPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
					$this->attackingPlayer['command_response'] = $response;
					$this->attackingPlayer->save();
					
					$this->duelAttackedUser = $this->attackingPlayer->getUser();
					
					$this->game['inter_turn'] = $this->attackingPlayer['id'];
					$this->game->save();
				} else {
					// pcha sa sem niekto kto tu vobec nema co robit
				}
				$this->actualPlayer['command_response'] = '';
				$this->actualPlayer['phase'] = Player::PHASE_NONE;
				$this->actualPlayer->save();
			} else {
				$this->attackedPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
				$this->attackedPlayer->save(TRUE);

				$this->actualPlayer['phase'] = Player::PHASE_WAITING;
				$this->actualPlayer['bang_used'] = $this->actualPlayer['bang_used'] + 1;
				if ($this->useCharacter === TRUE) {
					if ($this->actualPlayer->getIsBelleStar() || $this->actualPlayer->getIsSlabTheKiller()) {
						$notices = $this->actualPlayer->getNoticeList();
						$notices['character_used'] = 1;
						$this->actualPlayer->setNoticeList($notices);
					}
				}
				$this->actualPlayer->save();

				$this->game['inter_turn'] = $this->attackedPlayer['id'];
				$this->game['inter_turn_reason'] = serialize(array('action' => 'bang', 'from' => $this->actualPlayer['id'], 'to' => $this->attackedPlayer['id']));
				$this->game->save();
			}
			// vyhodime kartu bang
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
		}
	}

	protected function generateMessages() {
		if ($this->attackedPlayer) {
			$attackedUser = $this->attackedPlayer->getUser();
		}
		
		if ($this->check == self::OK) {
			if ($this->interTurnReason['action'] == 'duel') {
				$message = array(
					'text' => $this->loggedUser['username'] . ' pouzil Bang a teraz sa musí branit ' . $this->duelAttackedUser['username'],
					'notToUser' => $this->duelAttackedUser['id'],
				);
				$this->addMessage($message);

				$message = array(
					'text' => $this->loggedUser['username'] . ' pouzil Bang a teraz sa musis branit ty',
					'toUser' => $this->duelAttackedUser['id'],
				);
				$this->addMessage($message);
			} elseif ($this->interTurnReason['action'] == 'indians') {
				$message = array(
					'text' => $this->loggedUser['username'] . ' zabil svojho indiana',
					'notToUser' => $this->loggedUser['id'],
				);
				$this->addMessage($message);

				$message = array(
					'text' => 'zabil si svojho indiana',
					'toUser' => $this->loggedUser['id'],
				);
				$this->addMessage($message);
			} else {
				$message = array(
					'text' => $this->loggedUser['username'] . ' zautocil Bangom na ' . $attackedUser['username'],
					'notToUser' => $attackedUser['id'],
				);
				$this->addMessage($message);

				$message = array(
					'text' => $this->loggedUser['username'] . ' na teba zautocil Bangom',
					'toUser' => $attackedUser['id'],
				);
				$this->addMessage($message);
			}
		} elseif ($this->check == self::NOT_YOUR_TURN) {
			$message = array(
				'text' => 'nemozes strielat nie si na tahu',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_PLAY_BANG) {
			$message = array(
				'text' => 'nemozes hrat kartu bang',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::DO_NOT_HAVE_BANG) {
			$message = array(
				'text' => 'nemas kartu bang',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_PLAY_BANG_AGAINST_YOURSELF) {
			$message = array(
				'text' => 'nemozes pouzit kartu bang sam proti sebe',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::PLAYER_IS_NOT_IN_GAME) {
			$message = array(
				'text' => 'hrac "' . $this->params[0] . '" nehra v tejto hre',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_PLAY_BANG_AGAINST_DEAD_PLAYER) {
			$message = array(
				'text' => 'nemozes utocit na mrtveho hraca',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::PLAYER_IS_TOO_FAR) {
			$message = array(
				'text' => 'Nedostrelis, ' .  $attackedUser['username'] . ' je prilis daleko',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::USED_BANG_ALREADY) {
			$message = array(
				'text' => 'V tomto kole si uz pouzil bang',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
		if ($this->check == self::OK) {
			if ($this->interTurnReason['action'] == 'duel') {
				
			} elseif ($this->interTurnReason['action'] == 'indians') {
				
			} else {
				// TODO prerobit tak aby to fungovalo aj bez javascriptu, onclick treba nahradit niecim inym, pripadne doplnit tlacitko ktore skryje ten overlay

				MySmarty::assign('card', $this->bangCard);
				$response = MySmarty::fetch($this->template);
				$this->attackedPlayer['command_response'] = $response;
				$this->attackedPlayer->save();
			}
		}

		return '';
	}
}

?>