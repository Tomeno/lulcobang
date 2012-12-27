<?php

class SombreroCommand extends DefensiveCommand {
	protected function run() {
		if ($this->check == DefensiveCommand::OK) {
			// odhodime sombrero
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
			$this->runMollyStarkAction();
			$this->changeInterturn();
		}
	}

	protected function generateMessages() {
		if ($this->check == DefensiveCommand::OK) {
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
		} elseif ($this->check == DefensiveCommand::CANNOT_PLAY_CARD) {
			$message = array(
				'text' => 'nemozes pouzit kartu sombrero',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::YOU_ARE_UNDER_INDIANS_ATTACK) {
			$message = array(
				'text' => 'Proti utoku indianov nemozes pouzit sombrero',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::YOU_ARE_UNDER_DUEL_ATTACK) {
			$message = array(
				'text' => 'Proti duelu nemozes pouzit sombrero',
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