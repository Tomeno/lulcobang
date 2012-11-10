<?php

class CanteenCommand extends Command {
	
	const OK = 1;
	
	const TOO_MANY_LIFES = 2;
	
	protected function check() {
		if ($this->actualPlayer['actual_lifes'] < $this->actualPlayer['max_lifes']) {
			$this->check = self::OK;
		} else {
			$this->check = self::TOO_MANY_LIFES;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			$additionalLifes = 1;
			
			$newLifes = min($this->actualPlayer['actual_lifes'] + $additionalLifes, $this->actualPlayer['max_lifes']);
			$this->actualPlayer['actual_lifes'] = $newLifes;
			$this->actualPlayer->save();

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil cutoru na doplnenie zivota',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si cutoru na doplnenie zivota',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::TOO_MANY_LIFES) {
			$message = array(
				'text' => 'nemozes pouzit cutoru, mas plnu nadrz',
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