<?php

class CancanCommand extends Command {

	protected $place = 'hand';

	const OK = 1;
	
	const NO_CARDS_ON_HAND = 2;

	const NO_CARDS_ON_THE_TABLE = 3;
	
	const PLAYER_NOT_SELECTED = 4;
	
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
			if (isset($this->params[1]) && $this->params[1] != 'hand') {
				$methods = array('hasAllCardsOnTheTableOrOnWait');
				$enemyPlayerHasCardsChecker = new EnemyPlayerHasCardsChecker($this, $methods);
				$enemyPlayerHasCardsChecker->setCards(array($this->params[1]));
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
			$this->check = self::PLAYER_NOT_SELECTED;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
			GameUtils::throwCards($this->game, $this->enemyPlayer, $this->enemyPlayersCards[$this->enemyPlayer['id']], $this->place);
			
			if ($this->place == 'table') {
				// kedze je mozne ze rusime nejaku modru kartu ktora ovplyvnuje vzdialenost, preratame maticu
				// ak to bude velmi pomale, budeme to robit len ak je medzi zrusenymi kartami fakt takato karta
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
				'text' => $this->loggedUser['username'] . ' pouzil can can na odobratie karty ' . $enemyUser['username'],
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);

			$message = array(
				'text' => 'pouzil si can can na odobratie karty ' . $enemyUser['username'],
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
		}
	}

	protected function createResponse() {
		return '';
	}
}
?>