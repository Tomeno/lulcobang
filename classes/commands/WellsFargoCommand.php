<?php

class WellsFargoCommand extends Command {

	protected $wellsFargoCard = NULL;

	const OK = 1;

	protected function check() {
		$res = $this->actualPlayer->getHasWellsFargoOnHand();
		if ($res) {
			$this->wellsFargoCard = $res;
			$this->check = self::OK;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			GameUtils::throwCards($this->game, $this->actualPlayer, array($this->wellsFargoCard));
			$drawnCards = GameUtils::drawCards($this->game, 3);

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
		// TODO
	}

	protected function createResponse() {
		// TODO
	}
}

?>