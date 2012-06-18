<?php

class ThrowCommand extends Command {

	protected $thrownCards = array();

	const OK = 1;

	const NO_CARDS = 2;

	const NOT_YOUR_TURN = 3;

	const NO_GAME = 4;

	protected function check() {
		
	}
	protected function run() {
		$place = $this->params[1];
		if (!$place) {
			$place = 'hand';
		}
		GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, $place);
	}

	protected function generateMessages() {
	
	}

	protected function createResponse() {

	}
}

?>