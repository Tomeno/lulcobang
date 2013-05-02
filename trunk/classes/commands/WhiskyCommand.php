<?php

class WhiskyCommand extends Command {
	
	const OK = 1;
	
	const TOO_MANY_LIFES = 2;
	
	const ADDITIONAL_CARD_NOT_IN_HAND = 3;
	
	const YOU_HAVE_TO_USE_ADDITIONAL_CARD = 4;
	
	protected function check() {
		if (isset($this->params['additionalCardsName'])) {
			$additionalCardTitle = $this->params['additionalCardsName'];
			$method = 'getHas' . ucfirst($additionalCardTitle) . 'OnHand';
			$additionalCard = $this->actualPlayer->$method();

			// TODO skontrolovat ci additional card je ina ako whisky (v AI mode je mozne ze to bude ta ista karta)
			
			if ($additionalCard) {
				$this->cards[] = $additionalCard;
				if ($this->actualPlayer['actual_lifes'] < $this->actualPlayer['max_lifes']) {
					$this->check = self::OK;
				} else {
					$this->check = self::TOO_MANY_LIFES;
				}
			} else {
				$this->check = self::ADDITIONAL_CARD_NOT_IN_HAND;
			}
		} else {
			$this->check = self::YOU_HAVE_TO_USE_ADDITIONAL_CARD;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			$additionalLifes = 2;
			$newLifes = min($this->actualPlayer['actual_lifes'] + $additionalLifes, $this->actualPlayer['max_lifes']);
			$this->actualPlayer['actual_lifes'] = $newLifes;
			$this->actualPlayer->save();

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil whisky na doplnenie 2 zivotov',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si whisky na doplnenie 2 zivotov',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::TOO_MANY_LIFES) {
			$user = $this->actualPlayer->getUser();
			$message = array(
				'text' => 'nemozes pouzit whisky, mas plnu nadrz',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::ADDITIONAL_CARD_NOT_IN_HAND) {
			$message = array(
				'text' => 'nemas na ruke "' . $this->params['additionalCardsName'] . '"',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::YOU_HAVE_TO_USE_ADDITIONAL_CARD) {
			$message = array(
				'text' => 'Musis vyhodit este jednu kartu',
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