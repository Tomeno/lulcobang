<?php

class BangCommand extends Command {
	const OK = 1;
	
	const DO_NOT_HAVE_BANG = 2;
	
	const NOT_YOUR_TURN = 3;
	
	const CANNOT_PLAY_BANG = 4;
	
	const CANNOT_PLAY_BANG_AGAINST_YOURSELF = 5;
	
	const PLAYER_IS_NOT_IN_GAME = 6;
	
	const CANNOT_PLAY_BANG_AGAINST_DEAD_PLAYER = 7;
	
	protected $bangCard = NULL;

	protected $attackedPlayer = NULL;

	protected $template = 'you-are-attacked.tpl';

	protected function check() {
		if ($this->actualPlayer['phase'] == Player::PHASE_PLAY) {
			
			// TODO spravit k tomuto nejaku metodu v commande lebo sa to pouziva dost casto
			// TODO zistit ci je hrac este zivy
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
							$this->check = self::OK;
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
		} elseif ($this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
			if ($this->interTurnReason['action'] == 'indians') {
				// TODO check player is under attack and this is his interturn
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
				$nextPosition = GameUtils::getNextPosition($this->game, $this->actualPlayer['position']);
				foreach ($this->players as $player) {
					if ($player['id'] == $this->actualPlayer['id']) {
						$this->actualPlayer['command_response'] = '';
						$this->actualPlayer['phase'] = Player::PHASE_NONE;
						$this->actualPlayer->save();
					} else {
						if ($player['position'] == $nextPosition) {
							$nextPositionPlayer = $player;
							$player['phase'] = Player::PHASE_UNDER_ATTACK;
							$player->save();
						}
					}
				}

				// nastavime interturn
				$this->game['inter_turn_reason'] = serialize(array('action' => 'indians', 'from' => $this->attackingPlayer['id'], 'to' => $nextPositionPlayer['id']));
				$this->game['inter_turn'] = $nextPosition;
				$this->game->save();

				// vyhodime kartu bang
				GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			} else {

				$this->attackedPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
				$this->attackedPlayer->save(TRUE);

				$this->actualPlayer['phase'] = Player::PHASE_WAITING;
				$this->actualPlayer->save();

				// TODO toto plati len ak je to utok bangom, ale bang sa pouziva na viacerych miestach - premysliet a dorobit aj duel a indianov prip dalsie
				$this->game['inter_turn'] = $this->attackedPlayer['position'];
				$this->game['inter_turn_reason'] = serialize(array('action' => 'bang', 'from' => $this->actualPlayer['id'], 'to' => $this->attackedPlayer['id']));
				$this->game->save();

				// TODO nastavit ze hrac pouzil bang ak sa jedna o jeho utok na niekoho pomocou bangu

				$retval = GameUtils::throwCards($this->game, $this->actualPlayer, array($this->bangCard));
			}
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$attackedUser = $this->attackedPlayer->getUser();
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
		}
	}

	protected function createResponse() {
		if ($this->check == self::OK) {
			// TODO prerobit tak aby to fungovalo aj bez javascriptu, onclick treba nahradit niecim inym, pripadne doplnit tlacitko ktore skryje ten overlay

			MySmarty::assign('card', $this->bangCard);
			$response = MySmarty::fetch($this->template);
			$this->attackedPlayer['command_response'] = $response;
			$this->attackedPlayer->save();
		}

		return '';
	}
}

?>