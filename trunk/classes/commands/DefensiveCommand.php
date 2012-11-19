<?php

abstract class DefensiveCommand extends Command {

	const OK = 1;
	
	const CANNOT_PLAY_CARD = 2;
	
	const YOU_ARE_UNDER_INDIANS_ATTACK = 3;
	
	const YOU_ARE_UNDER_DUEL_ATTACK = 4;
	
	protected function check() {
		if ($this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
			if ($this->interTurnReason['action'] == 'indians') {
				$this->check = self::YOU_ARE_UNDER_INDIANS_ATTACK;
			} elseif ($this->interTurnReason['action'] == 'duel') {
				$this->check = self::YOU_ARE_UNDER_DUEL_ATTACK;
			} else {
				$this->check = self::OK;
			}
		} else {
			$this->check = self::CANNOT_PLAY_CARD;
		}
	}
}

?>