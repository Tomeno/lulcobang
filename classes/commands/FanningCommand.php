<?php

class FanningCommand extends Command {
	const OK = 1;
	
	const CANNOT_ATTACK_YOURSELF = 2;
	
	const PLAYER_IS_NOT_IN_GAME = 3;
	
	const CANNOT_ATTACK_DEAD_PLAYER = 4;
	
	const PLAYER_IS_TOO_FAR = 5;

	const SELECT_ADDITIONAL_PLAYER = 6;
	
	const ADDITIONAL_PLAYER_IS_TOO_FAR = 7;

	protected $template = 'you-are-attacked.tpl';

	protected function check() {
		$attackedPlayer = $this->params['enemyPlayerUsername'];
		if ($this->loggedUser['username'] != $attackedPlayer) {
			if ($this->attackedPlayer !== NULL) {
				if ($this->attackedPlayer['actual_lifes'] > 0) {
					$distance = $this->game->getDistance($this->loggedUser['username'], $attackedPlayer);
					if ($distance <= $this->actualPlayer->getRange($this->game)) {
						$playersDistanceOne = $this->attackedPlayer->findTargetsInDistance($this->game, 1);
						
						$possibleFanningTargets = array();
						foreach ($playersDistanceOne as $player) {
							if ($player['id'] != $this->actualPlayer['id']) {
								$possibleFanningTargets[] = $player;
							}
						}
						
						if ($possibleFanningTargets) {
							$additionalAttackedUser = $this->params['additionalEnemyPlayerUsername'];
							if ($additionalAttackedUser) {
								$distance = $this->game->getDistance($additionalAttackedUser, $attackedPlayer);
								if ($distance > 1) {
									$this->check = self::ADDITIONAL_PLAYER_IS_TOO_FAR;
								} else {
									$this->check = self::OK;
								}
							} else {
								$this->check = self::SELECT_ADDITIONAL_PLAYER;
							}
						} else {
							$this->check = self::OK;
						}
					} else {
						$this->check = self::PLAYER_IS_TOO_FAR;
					}
				} else {
					$this->check = self::CANNOT_ATTACK_DEAD_PLAYER;
				}
			} else {
				$this->check = self::PLAYER_IS_NOT_IN_GAME;
			}
		} else {
			$this->check = self::CANNOT_ATTACK_YOURSELF;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			$this->attackedPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
			$this->attackedPlayer->save();

			if ($this->params['additionalEnemyPlayerId']) {
				$playerRepository = new PlayerRepository();
				$additionalAttackedPlayer = $playerRepository->getOneById(intval($this->params['additionalEnemyPlayerId']));
				MySmarty::assign('card', $this->cards[0]);
				$response = MySmarty::fetch($this->template);
				$additionalAttackedPlayer['command_response'] = $response;
				$additionalAttackedPlayer->save();
			}
			
			$this->actualPlayer['phase'] = Player::PHASE_WAITING;
			if ($this->useCharacter === TRUE) {
				if ($this->actualPlayer->getIsBelleStar($this->game)) {
					$notices = $this->actualPlayer->getNoticeList();
					$notices['character_used'] = 1;
					$this->actualPlayer->setNoticeList($notices);
				}
			}
			$this->actualPlayer->save();

			// TODO toto plati len ak je to utok bangom, ale bang sa pouziva na viacerych miestach - premysliet a dorobit aj duel a indianov prip dalsie
			$this->game['inter_turn'] = $this->attackedPlayer['id'];
			$this->game['inter_turn_reason'] = serialize(array(
				'action' => 'fanning',
				'from' => $this->actualPlayer['id'],
				'to' => $this->attackedPlayer['id'],
				'additionalTo' => intval($this->params['additionalEnemyPlayerId']),
			));
			$this->game->save();

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
		}
	}

	protected function generateMessages() {
		if ($this->attackedPlayer) {
			$attackedUser = $this->attackedPlayer->getUser();
		}
		
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' zautocil roztriestenou strelou na ' . $attackedUser['username'],
				'notToUser' => $attackedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' na teba zautocil roztriestenou strelou',
				'toUser' => $attackedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_ATTACK_YOURSELF) {
			$message = array(
				'text' => 'nemozes zautocit sam na seba',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::PLAYER_IS_NOT_IN_GAME) {
			$message = array(
				'text' => 'hrac "' . $this->params['enemyPlayerUsername'] . '" nehra v tejto hre',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_ATTACK_DEAD_PLAYER) {
			$message = array(
				'text' => 'nemozes utocit na mrtveho hraca',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::PLAYER_IS_TOO_FAR) {
			$message = array(
				'text' => 'nemozes utocit na hraca ' .  $attackedUser['username'] . ', je prilis daleko',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::SELECT_ADDITIONAL_PLAYER) {
			$message = array(
				'text' => 'Musis vybrat este jedneho hraca, ktory je vo vzdialenosti 1 od tvojho primarneho ciela',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::ADDITIONAL_PLAYER_IS_TOO_FAR) {
			$message = array(
				'text' => 'Druhy hrac musi byt vo vzdialenosti 1 od tvojho primarneho ciela',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
		if ($this->check == self::OK) {
			// TODO prerobit tak aby to fungovalo aj bez javascriptu, onclick treba nahradit niecim inym, pripadne doplnit tlacitko ktore skryje ten overlay

			MySmarty::assign('card', $this->cards[0]);
			$response = MySmarty::fetch($this->template);
			$this->attackedPlayer['command_response'] = $response;
			$this->attackedPlayer->save();
		}

		return '';
	}
}

?>