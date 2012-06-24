<?php

class MissedCommand extends Command {

	protected function check() {
		
	}

	protected function run() {
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

	protected function generateMessages() {
	}

	protected function createResponse() {
		;
	}
}

?>