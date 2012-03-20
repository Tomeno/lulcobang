<?php

class ThrowCommand extends Command {

	protected $thrownCards = array();

	const OK = 1;

	const NO_CARDS = 2;

	const NOT_YOUR_TURN = 3;

	const NO_GAME = 4;

	protected function check() {
		if ($this->game && $this->game['status'] == Game::GAME_STATUS_STARTED) {
			$playerOnTurn = $this->game->getPlayerOnTurn();
			if ($playerOnTurn['id'] == $this->actualPlayer['id']) {
			// aj tu budu chodit parametre bud ako string - nazov karty, alebo idecko vo forme card-xy231zq - co bude unikatne idecko karty pre danu hru pocas aktualneho pouzivania
			// todo posledny parameter bude hand alebo table a urci ci sa mame pozriet na ruku alebo na stol
				$card = ucfirst($this->params[0]);
				$method = 'getHas' . $card . 'OnHand';
				$res = $this->actualPlayer->$method();
				if ($res) {
					$this->thrownCards[] = $res;
					$this->check = self::OK;
				} else {
					$this->check = self::NO_CARDS;
				}
			} else {
				$this->check = self::NOT_YOUR_TURN;
			}
		} else {
			self::NO_GAME;
		}
	}
	protected function run() {
		if ($this->check == self::OK) {
			$throwPile = unserialize($this->game['throw_pile']);

			$playerCards = unserialize($this->actualPlayer['hand_cards']);

			$thrownCardsIds = array();
			foreach ($this->thrownCards as $card) {
				$thrownCardsIds[] = $card['id'];
			}

			$newPlayerCards = array();
			foreach ($playerCards as $playerCard) {
				if (in_array($playerCard, $thrownCardsIds)) {
					$throwPile[] = $playerCard;
				} else {
					$newPlayerCards[] = $playerCard;
				}
			}
			$this->game['throw_pile'] = serialize($throwPile);
			$this->game->save();

			$this->actualPlayer['hand_cards'] = serialize($newPlayerCards);
			$this->actualPlayer->save();
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			echo 'OK';
		} else {
			echo 'KO';
		}
	}

	protected function createResponse() {

	}
}

?>