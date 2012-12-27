<?php

class IronPlateCommand extends DefensiveCommand {
	protected function run() {
		if ($this->check == DefensiveCommand::OK) {
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
			$this->runMollyStarkAction();
			$this->changeInterturn();
		}
	}

	protected function generateMessages() {
		if ($this->check == DefensiveCommand::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil iron plate na zachranu zivota',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si iron plate na zachranu zivota',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::YOU_ARE_UNDER_INDIANS_ATTACK) {
			$message = array(
				'text' => 'Proti utoku indianov nemozes pouzit iron plate',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::YOU_ARE_UNDER_DUEL_ATTACK) {
			$message = array(
				'text' => 'Proti duelu nemozes pouzit iron plate',
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