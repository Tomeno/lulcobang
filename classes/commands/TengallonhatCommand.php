<?php

/**
 * TODO vytvorit triedu DefendCommand kde bude switch na rozne typy utokov ktorym sa hrac moze branit a podla toho typu sa bude nastavovat dalsi hrac a stav hry
 */

class TengallonhatCommand extends Command {

	const OK = 1;
	
	protected function check() {
		$this->check = self::OK;
	}
	
	protected function run() {
		if ($this->check == self::OK) {
			// odhodime ten gallon hat
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
			
			$this->changeInterturn();
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil Gallon Hat na zachranu zivota',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si Gallon Hat na zachranu zivota',
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