<?php

class BibleCommand extends DefensiveCommand {
	protected function run() {
		if ($this->check == DefensiveCommand::OK) {
			// odhodime kartu biblia
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');

			// potiahneme kartu
			$drawnCards = GameUtils::drawCards($this->game, 1);
			$handCards = unserialize($this->actualPlayer['hand_cards']);
			$handCards = array_merge($handCards, $drawnCards);
			$this->actualPlayer['hand_cards'] = serialize($handCards);
			
			$this->changeInterturn();
		}
	}

	protected function generateMessages() {
		if ($this->check == DefensiveCommand::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil bibliu na zachranu zivota',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si bibliu na zachranu zivota',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::CANNOT_PLAY_CARD) {
			$message = array(
				'text' => 'nemozes pouzit kartu biblia',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::YOU_ARE_UNDER_INDIANS_ATTACK) {
			$message = array(
				'text' => 'Proti utoku indianov nemozes pouzit bibliu',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::YOU_ARE_UNDER_DUEL_ATTACK) {
			$message = array(
				'text' => 'Proti duelu nemozes pouzit bibliu',
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