<?php

class GeneralStoreCommand extends Command {
	
	const OK = 1;

	protected function check() {
		$this->check = self::OK;
	}

	protected function run() {
		if ($this->check == self::OK) {
			$livePlayers = 0;
			foreach ($this->getPlayers() as $player) {
				if ($player['actual_lifes'] > 0) {
					$livePlayers++;
				}
			}
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			$drawnCards = GameUtils::drawCards($this->game, $livePlayers);

			// TODO vymysliet ako to spravit
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => 'pouzil si kartu obchod',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil kartu obchod',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
		// TODO
	}
}

?>