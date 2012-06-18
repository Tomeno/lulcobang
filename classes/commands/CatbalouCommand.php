<?php

class CatbalouCommand extends Command {

	protected $place = 'hand';


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
					$this->check = 1;
					$this->place = $enemyPlayerHasCardsChecker->getPlace();
				}
			} else {
				$handCards = $this->enemyPlayer->getHandCards();
				$card = $handCards[array_rand($handCards)];
				if ($card) {
					$this->addEnemyPlayerCard($this->enemyPlayer, $card);
					$this->check = 1;
					$this->place = 'hand';
				} else {
					// TODO nema karty na ruke
				}
			}
		}
	}

	protected function run() {
		if ($this->check == 1) {
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			GameUtils::throwCards($this->game, $this->enemyPlayer, $this->enemyPlayersCards[$this->enemyPlayer['id']], $this->place);
		}
	}

	protected function generateMessages() {

	}

	protected function createResponse() {
		return '';
	}
}
?>