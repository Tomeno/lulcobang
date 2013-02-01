<?php

class DrawHighNoonCommand extends Command {
	
	const OK = 1;
	
	
	protected function check() {
		if ($this->game->getIsHighNoon()) {
			if ($this->game->getHighNoonPile()) {
				if ($this->actualPlayer->getRoleObject()->getIsSheriff()) {
					if ($this->actualPlayer['phase'] == Player::PHASE_DRAW_HIGH_NOON) {
						$this->check = self::OK;
					} else {
						// nie si vo faze tahania high noon
					}
				} else {
					// nie si serif
				}
				
			} else {
				// uz nemame karty v high noon baliku
			}
		} else {
			// nehrame high noon rozsirenie
		}
	}
	
	protected function run() {
		if ($this->check == 1) {
			$this->actualPlayer['phase'] = $this->getNextPhase($this->actualPlayer);
			$this->actualPlayer->save();
			
			$highNoonPile = unserialize($this->game['high_noon_pile']);
			$drawnCard = array_pop($highNoonPile);
			$this->game['high_noon'] = $drawnCard;
			$this->game['high_noon_pile'] = serialize($highNoonPile);
			$this->game = $this->game->save(TRUE);
			
			$this->executeSpecialAction();
			
			// TODO ked prichadza a odchadza hangover treba preratat vzdialenosti
			
			// TODO ked prichadza High noon, treba nastavovat fazy inak
			
			// TODO prichod doktora
		}
	}
	
	protected function executeSpecialAction() {
		if ($this->game->getIsHNTheDoctor()) {
			$min = 100;
			foreach ($this->game->getPlayers() as $player) {
				if ($player['actual_lifes'] > 0) {
					if ($player['actual_lifes'] < $min) {
						$min = $player['actual_lifes'];
					}
				}
			}
			
			foreach ($this->game->getPlayers() as $player) {
				if ($player['actual_lifes'] > 0 && $player['actual_lifes'] == $min) {
					$player['actual_lifes'] = min($player['max_lifes'], $player['actual_lifes'] + 1);
					$player->save();
					
					// TODO messages
				}
			}
		}
	}
	
	protected function generateMessages() {
		;
	}
	
	protected function createResponse() {
		;
	}
	
}

?>