<?php

class SaloonCommand extends Command {

	const OK = 1;
	
	protected function check() {
		$this->check = self::OK;
	}

	protected function run() {
		foreach ($this->players as $player) {
			if ($player['actual_lifes'] > 0) {
				$newLifes = min($player['actual_lifes'] + 1, $player['max_lifes']);
				$player['actual_lifes'] = $newLifes;
				$player->save();
			}
		}

		GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil saloon a doplnil kazdemu jeden zivota',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si saloon na doplnenie zivota kazdemu hracovi',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
	}
}

?>