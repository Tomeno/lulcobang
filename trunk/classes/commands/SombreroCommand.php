<?php

/**
 * TODO vytvorit triedu DefendCommand kde bude switch na rozne typy utokov ktorym sa hrac moze branit a podla toho typu sa bude nastavovat dalsi hrac a stav hry
 */

class SombreroCommand extends Command {

	const OK = 1;
	
	protected function check() {
		$this->check = self::OK;
	}
	
	protected function run() {
		if ($this->check == self::OK) {
			// odhodime sombrero
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
			
			$this->changeInterturn();
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil sombrero na zachranu zivota',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si sombrero na zachranu zivota',
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