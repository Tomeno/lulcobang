<?php

class RagTimeCommand extends Command {

	protected $place = 'hand';
	
	const OK = 1;

	const NO_CARDS_ON_HAND = 2;

	const NO_CARDS_ON_THE_TABLE = 3;
	
	const PLAYER_NOT_SELECTED = 4;
	
	const ADDITIONAL_CARD_NOT_IN_HAND = 5;
	
	const YOU_HAVE_TO_USE_ADDITIONAL_CARD = 6;
	
	protected function check() {
		// TODO spravit prechecker
		$attackedPlayer = $this->params[0];
		foreach ($this->players as $player) {
			$user = $player->getUser();
			if ($user['username'] == $attackedPlayer) {
				$this->enemyPlayer = $player;
				break;
			}
		}

		if ($this->enemyPlayer) {
			if ($this->params[1]) {
				$additionalCardTitle = $this->params[1];
				$method = 'getHas' . ucfirst($additionalCardTitle) . 'OnHand';
				$additionalCard = $this->actualPlayer->$method();

				if ($additionalCard) {
					$this->cards[] = $additionalCard;

					if (isset($this->params[2]) && $this->params[2] != 'hand') {
						$methods = array('hasAllCardsOnTheTableOrOnWait');
						$enemyPlayerHasCardsChecker = new EnemyPlayerHasCardsChecker($this, $methods);
						$enemyPlayerHasCardsChecker->setCards(array($this->params[2]));
						if ($enemyPlayerHasCardsChecker->check()) {
							$this->check = self::OK;
							$this->place = $enemyPlayerHasCardsChecker->getPlace();
						} else {
							$this->check = self::NO_CARDS_ON_THE_TABLE;
						}
					} else {
						$handCards = $this->enemyPlayer->getHandCards();
						$card = $handCards[array_rand($handCards)];
						if ($card) {
							$this->addEnemyPlayerCard($this->enemyPlayer, $card);
							$this->check = self::OK;
							$this->place = 'hand';
						} else {
							$this->check = self::NO_CARDS_ON_HAND;
						}
					}
				} else {
					$this->check = self::ADDITIONAL_CARD_NOT_IN_HAND;
				}
			} else {
				$this->check = self::YOU_HAVE_TO_USE_ADDITIONAL_CARD;
			}
		} else {
			$this->check = self::PLAYER_NOT_SELECTED;
		}
	}

	protected function run() {
		if ($this->check == 1) {
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			GameUtils::moveCards($this->game, $this->enemyPlayersCards[$this->enemyPlayer['id']], $this->enemyPlayer, 'hand', $this->actualPlayer, $this->place);
			
			if ($this->place == 'table') {
				// kedze je mozne ze berieme nejaku modru kartu ktora ovplyvnuje vzdialenost, preratame maticu
				// ak to bude velmi pomale, budeme to robit len ak je medzi zobratymi kartami fakt takato karta
				$matrix = GameUtils::countMatrix($this->game);
				$this->game['distance_matrix'] = serialize($matrix);
				$this->game->save();
			}
		}
	}

	protected function generateMessages() {
		if ($this->enemyPlayer) {
			$enemyUser = $this->enemyPlayer->getUser();
		}
		if ($this->check == self::OK) {
			// TODO doplnit v hlaske aj miesto odkial bola karta zobrata
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil Rag Time na odobratie karty ' . $enemyUser['username'],
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);

			$message = array(
				'text' => 'pouzil si Rag Time na odobratie karty ' . $enemyUser['username'],
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NO_CARDS_ON_HAND) {
			$message = array(
				'text' => $enemyUser['username'] . ' nema ziadne karty na ruke',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NO_CARDS_ON_THE_TABLE) {
			$message = array(
				'text' => $enemyUser['username'] . ' nema ziadne karty na stole',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::PLAYER_NOT_SELECTED) {
			$message = array(
				'text' => 'nevybral si ziadneho hraca',
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
		return '';
	}
}
?>