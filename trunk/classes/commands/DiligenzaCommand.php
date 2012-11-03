<?php

class DiligenzaCommand extends Command {
	
	protected $diligenzaCard = NULL;

	const OK = 1;

	protected function check() {
		$method = 'getHasDiligenzaOnHand';
		$res = $this->actualPlayer->$method();
		if ($res) {
			$this->diligenzaCard = $res;
			$this->check = self::OK;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			GameUtils::throwCards($this->game, $this->actualPlayer, array($this->diligenzaCard));
			$drawnCards = GameUtils::drawCards($this->game, 2);

			// TODO toto je asi miesto kde sa zvysuje pocet kariet - v game utils throw cards sa karta zahodi ale tu este asi je
			$handCards = unserialize($this->actualPlayer['hand_cards']);
			foreach ($drawnCards as $drawnCard) {
				$handCards[] = $drawnCard;
			}
			$this->actualPlayer['hand_cards'] = serialize($handCards);
			$this->actualPlayer->save();
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => 'pouzil si kartu diligenza',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil kartu diligenza',
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