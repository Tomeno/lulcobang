<?php

class MissedCommand extends Command {

	const OK = 1;
	
	const CANNOT_PLAY_CARD = 2;
	
	protected function check() {
		if ($this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
			$this->check = self::OK;
		} else {
			$this->check = self::CANNOT_PLAY_CARD;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			$this->actualPlayer['phase'] = Player::PHASE_NONE;
			$this->actualPlayer['command_response'] = '';
			$this->actualPlayer->save();

			// TODO toto asi nebudeme moct nastavovat hned ako jeden hrac da missed - pretoze tu mozu byt aj multiutoky (gulomet, indiani)
			$this->attackingPlayer['phase'] = Player::PHASE_PLAY;
			$this->attackingPlayer->save();

			// TODO toto takisto nebudeme moct nastavovat hned kvoli multiutokom
			$this->game['inter_turn'] = 0;
			$this->game['inter_turn_reason'] = '';
			$this->game->save();
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
		}
	}

	protected function createResponse() {
		;
	}
}

?>