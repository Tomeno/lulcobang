<?php

class UseOneRoundCardCommand extends Command {
	
	const OK = 1;
	
	protected function check() {
		if ($this->game->getIsHNHardLiquor()) {
			$phaseChecker = new PlayerPhaseChecker($this, array('isInDrawPhase'));
			$check = $phaseChecker->check();
			if ($check === TRUE) {
				$this->check = self::OK;
			}
		}
	}
	
	protected function run() {
		if ($this->check == self::OK) {
			if ($this->game->getIsHNHardLiquor()) {
				$this->hardLiquor();
			}
		}
	}
	
	protected function hardLiquor() {
		$actualLifes = $this->actualPlayer['actual_lifes'];
		$newActualLifes = min($this->actualPlayer['max_lifes'], $actualLifes + 1);
		$this->actualPlayer['actual_lifes'] = $newActualLifes;
		$this->actualPlayer['phase'] = Player::PHASE_PLAY;
		$this->actualPlayer->save();
	}
	
	protected function createResponse() {
		;
	}

	protected function generateMessages() {
		
	}
}

?>