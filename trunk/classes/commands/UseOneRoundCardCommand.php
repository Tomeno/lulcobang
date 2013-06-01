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
		} elseif ($this->game->getIsHNBloodBrothers()) {
			if (!$this->actualPlayer->getGiveLifeInBloodBrothers()) {
				$this->check = self::OK;
				// TODO moze to robit len na zaciatku tahu - v draw faze? kedy bude moct venovat zivot gary looter?
				
				// TODO nesmie to byt posledny zivot
				
				// TODO asi by to nemal byt hrac ktory ma plny pocet zivotov
			} else {
				// TODO uz pouzil blood brothers
			}
		}
	}
	
	protected function run() {
		if ($this->check == self::OK) {
			if ($this->game->getIsHNHardLiquor()) {
				$this->hardLiquor();
			} elseif ($this->game->getIsHNBloodBrothers()) {
				$this->bloodBrothers();
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
	
	protected function bloodBrothers() {
		$notices = $this->actualPlayer->getNoticeList();
		$notices['blood_brothers'] = 1;
		$this->actualPlayer->setNoticeList($notices);
		$this->actualPlayer['actual_lifes'] = $this->actualPlayer['actual_lifes'] - 1;
		$this->actualPlayer->save();
		
		$this->attackedPlayer['actual_lifes'] = $this->attackedPlayer['actual_lifes'] + 1;
		$this->attackedPlayer->save();
	}

	protected function createResponse() {
		;
	}

	protected function generateMessages() {
		
	}
}

?>