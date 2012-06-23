<?php

class BeerCommand extends Command {
	
	protected function check() {
		// TODO checker ci uz nema dosiahnuty max lifes
	}

	protected function run() {
		$additionalLifes = 1;
		if ($this->actualPlayer->getCharacter()->getIsTequilaJoe()) {
			$additionalLifes = 2;
		}
		$newLifes = min($this->actualPlayer['actual_lifes'] + $additionalLifes, $this->actualPlayer['max_lifes']);
		$this->actualPlayer['actual_lifes'] = $newLifes;
		$this->actualPlayer->save();
		
		GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
	}

	protected function generateMessages() {
	}

	protected function createResponse() {
		;
	}
}

?>