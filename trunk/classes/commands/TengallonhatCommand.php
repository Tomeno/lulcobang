<?php

class TengallonhatCommand extends DefensiveCommand {
	protected function run() {
		if ($this->check == DefensiveCommand::OK) {
			// odhodime ten gallon hat
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
			
			$this->changeInterturn();
		}
	}

	protected function generateMessages() {
		if ($this->check == DefensiveCommand::OK) {
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
		} elseif ($this->check == DefensiveCommand::CANNOT_PLAY_CARD) {
			$message = array(
				'text' => 'nemozes pouzit kartu Gallon Hat',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::YOU_ARE_UNDER_INDIANS_ATTACK) {
			$message = array(
				'text' => 'Proti utoku indianov nemozes pouzit Gallon Hat',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::YOU_ARE_UNDER_DUEL_ATTACK) {
			$message = array(
				'text' => 'Proti duelu nemozes pouzit Gallon Hat',
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