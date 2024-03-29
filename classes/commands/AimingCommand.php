<?php

class AimingCommand extends Command {
	const OK = 1;
	
	const CANNOT_ATTACK_YOURSELF = 5;
	
	const PLAYER_IS_NOT_IN_GAME = 6;
	
	const CANNOT_ATTACK_DEAD_PLAYER = 7;
	
	const ADDITIONAL_CARD_NOT_IN_HAND = 8;
	
	const YOU_HAVE_TO_USE_ADDITIONAL_CARD = 9;
	
	const ADDITIONAL_CARD_HAVE_TO_BE_BANG = 10;
	
	protected $template = 'you-are-attacked.tpl';

	protected function check() {
		if ($this->loggedUser['username'] != $this->params['enemyPlayerUsername']) {
			if (isset($this->params['additionalCardsName']) && $this->params['additionalCardsName']) {
				$additionalCardId = $this->params['additionalCardsId'];
				$additionalCard = $this->actualPlayer->getCardWithId('hand', $additionalCardId);

				if ($additionalCard) {
					if ($additionalCard->getIsBang()) {
						$this->cards[] = $additionalCard;

						if ($this->attackedPlayer !== NULL) {
							if ($this->attackedPlayer['actual_lifes'] > 0) {
								$this->check = self::OK;
							} else {
								$this->check = self::CANNOT_ATTACK_DEAD_PLAYER;
							}
						} else {
							$this->check = self::PLAYER_IS_NOT_IN_GAME;
						}
					} else {
						$this->check = self::ADDITIONAL_CARD_HAVE_TO_BE_BANG;
					}
				} else {
					$this->check = self::ADDITIONAL_CARD_NOT_IN_HAND;
				}
			} else {
				$this->check = self::YOU_HAVE_TO_USE_ADDITIONAL_CARD;
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
			
			if ($this->actualPlayer->getIsBelleStar($this->game)) {
				$notices = $this->actualPlayer->getNoticeList();
				$notices['character_used'] = 1;
				$this->actualPlayer->setNoticeList($notices);
			}
			$this->actualPlayer->save();

			// TODO toto plati len ak je to utok bangom, ale bang sa pouziva na viacerych miestach - premysliet a dorobit aj duel a indianov prip dalsie
			$this->game['inter_turn'] = $this->attackedPlayer['id'];
			$this->game['inter_turn_reason'] = serialize(array('action' => 'aiming', 'from' => $this->actualPlayer['id'], 'to' => $this->attackedPlayer['id']));
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
				'text' => $this->loggedUser['username'] . ' zautocil Dvojitou ranou na ' . $attackedUser['username'],
				'notToUser' => $attackedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' na teba zautocil Dvojitou ranou',
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
		} elseif ($this->check == self::ADDITIONAL_CARD_NOT_IN_HAND) {
			$message = array(
				'text' => 'nemas na ruke "' . $this->params['additionalCardsName'] . '"',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::YOU_HAVE_TO_USE_ADDITIONAL_CARD) {
			$message = array(
				'text' => 'Musis vyhodit este jednu kartu',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::ADDITIONAL_CARD_HAVE_TO_BE_BANG) {
			$message = array(
				'text' => 'Pridavna karta musi byt bang',
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