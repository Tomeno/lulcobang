<?php

class ThrowCommand extends Command {

	protected $thrownCards = array();

	const OK = 1;

	protected function check() {
		$this->check = self::OK;
		
	}
	
	protected function run() {
		if ($this->check == self::OK) {
			$place = $this->params[1];
			if (!$place) {
				$place = 'hand';
			}
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, $place);
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' odhodil kartu ' . $this->cards[0]->getTitle(),
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'odhodil si kartu ' . $this->cards[0]->getTitle(),
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {

	}
}

?>