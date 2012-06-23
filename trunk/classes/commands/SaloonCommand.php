<?php

class SaloonCommand extends Command {

	protected function check() {
	}

	protected function run() {
		foreach ($this->players as $player) {
			$newLifes = min($player['actual_lifes'] + 1, $player['max_lifes']);
			$player['actual_lifes'] = $newLifes;
			$player->save();
		}

		GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
	}

	protected function generateMessages() {
	}

	protected function createResponse() {
	}
}

?>