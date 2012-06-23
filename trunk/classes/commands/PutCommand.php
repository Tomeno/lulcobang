<?php

class PutCommand extends Command {

	protected $putCards = array();

	protected $place = 'table';

	const OK = 1;

	const NO_CARDS = 2;

	const NOT_YOUR_TURN = 3;

	const NO_GAME = 4;

	protected function check() {
		if ($this->game && $this->game['status'] == Game::GAME_STATUS_STARTED) {
			$playerOnTurn = $this->game->getPlayerOnTurn();
			if ($playerOnTurn['id'] == $this->actualPlayer['id']) {
				$card = ucfirst($this->params[0]);
				$method = 'getHas' . $card . 'OnHand';
				$res = $this->actualPlayer->$method();
				if ($res) {
					// ak je karta zelena, davame ju medzi cakatelov
					if ($res->getIsGreen()) {
						$this->place = 'wait';
					}
					$this->putCards[] = $res;
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
			GameUtils::moveCards($this->game, $this->putCards, $this->actualPlayer, $this->place);
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