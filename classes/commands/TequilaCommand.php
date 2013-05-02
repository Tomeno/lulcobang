<?php

class TequilaCommand extends Command {
	
	const OK = 1;
	
	const TOO_MANY_LIFES = 2;
	
	const ADDITIONAL_CARD_NOT_IN_HAND = 3;
	
	const YOU_HAVE_TO_USE_ADDITIONAL_CARD = 4;
	
//	protected $attackedPlayer = NULL;
	
	protected function check() {
//		$attackedPlayerName = $this->params[0];
//		foreach ($this->players as $player) {
//			$user = $player->getUser();
//			if ($user['username'] == $attackedPlayerName) {
//				$this->attackedPlayer = $player;
//				break;
//			}
//		}

		if ($this->attackedPlayer === NULL) {
			$this->attackedPlayer = $this->actualPlayer;
		}
		
		if (isset($this->params['additionalCardsName'])) {
			$additionalCardTitle = $this->params['additionalCardsName'];
			$method = 'getHas' . ucfirst($additionalCardTitle) . 'OnHand';
			$additionalCard = $this->actualPlayer->$method();

			// TODO skontrolovat ci additional card je ina ako tequila (v AI mode je mozne ze to bude ta ista karta)
			
			if ($additionalCard) {
				$this->cards[] = $additionalCard;
				if ($this->attackedPlayer['actual_lifes'] < $this->attackedPlayer['max_lifes']) {
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
			$additionalLifes = 1;
			
			$newLifes = min($this->attackedPlayer['actual_lifes'] + $additionalLifes, $this->attackedPlayer['max_lifes']);
			$this->attackedPlayer['actual_lifes'] = $newLifes;
			$this->attackedPlayer->save();

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$user = $this->attackedPlayer->getUser();
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil tequilu na doplnenie zivota hracovi ' . $user['username'],
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si tequilu na doplnenie zivota hracovi ' . $user['username'],
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::TOO_MANY_LIFES) {
			$user = $this->attackedPlayer->getUser();
			$message = array(
				'text' => 'nemozes pouzit tequilu, ' . $user['username'] . ' ma plnu nadrz',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::ADDITIONAL_CARD_NOT_IN_HAND) {
			$message = array(
				'text' => 'nemas na ruke "' . $this->params[1] . '"',
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