<?php

class PepperboxCommand extends Command {
	const OK = 1;
	
	const CANNOT_ATTACK_YOURSELF = 5;
	
	const PLAYER_IS_NOT_IN_GAME = 6;
	
	const CANNOT_ATTACK_DEAD_PLAYER = 7;
	
	const PLAYER_IS_TOO_FAR = 8;
		
	protected $attackedPlayer = NULL;

	protected $template = 'you-are-attacked.tpl';

	protected function check() {
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
					$attackedUser = $this->attackedPlayer->getUser();
					$distance = $this->game->getDistance($this->loggedUser['username'], $attackedUser['username']);
					if ($distance <= $this->actualPlayer->getRange($this->game)) {
						$this->check = self::OK;
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
			$this->game['inter_turn_reason'] = serialize(array('action' => 'pepperbox', 'from' => $this->actualPlayer['id'], 'to' => $this->attackedPlayer['id']));
			$this->game->save();

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
		}
	}

	protected function generateMessages() {
		if ($this->attackedPlayer) {
			$attackedUser = $this->attackedPlayer->getUser();
		}
		
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' zautocil Peperboxom na ' . $attackedUser['username'],
				'notToUser' => $attackedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' na teba zautocil Peperboxom',
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
				'text' => 'hrac "' . $this->params[0] . '" nehra v tejto hre',
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
				'text' => 'nedostrelis, ' .  $attackedUser['username'] . ' je prilis daleko',
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