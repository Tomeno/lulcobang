<?php

class MissedCommand extends Command {

	const OK = 1;
	
	const CANNOT_PLAY_CARD = 2;
	
	const YOU_ARE_UNDER_INDIANS_ATTACK = 3;
	
	protected function check() {
		// TODO ked je pod utokom indianov, nemoze pouzit vedla
		if ($this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
			if ($this->interTurnReason['action'] !== 'indians') {
				$this->check = self::OK;
			} else {
				$this->check = self::YOU_ARE_UNDER_INDIANS_ATTACK;
			}
		} else {
			$this->check = self::CANNOT_PLAY_CARD;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			$this->changeInterturn();
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil vedla na zachranu zivota',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si vedla na zachranu zivota',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_PLAY_CARD) {
			$message = array(
				'text' => 'nemozes pouzit kartu vedla',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::YOU_ARE_UNDER_INDIANS_ATTACK) {
			$message = array(
				'text' => 'Proti utoku indianov nemozes pouzit vedla',
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