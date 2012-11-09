<?php

class PutCommand extends Command {

	protected $putCards = array();

	protected $place = 'table';

	const OK = 1;

	const NO_CARDS = 2;

	const NOT_YOUR_TURN = 3;

	const NO_GAME = 4;
	
	const HAS_ANOTHER_WEAPON = 5;

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
					
					if ($res->getIsWeapon() && $this->actualPlayer->getHasGun()) {
						$this->check = self::HAS_ANOTHER_WEAPON;
					} else {
						$this->putCards[] = $res;
						$this->check = self::OK;
					}
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
			
			if ($this->place == 'table') {
				// kedze je mozne ze vykladame nejaku modru kartu, ktora ovplyvnuje vzdialenost, preratame maticu
				// ak to bude velmi pomale, budeme to robit len ak je medzi vylozenymi kartami fakt takato karta
				$matrix = GameUtils::countMatrix($this->game);
				$this->game['distance_matrix'] = serialize($matrix);
				$this->game->save();
			}
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			echo 'OK';
		} elseif ($this->check == self::HAS_ANOTHER_WEAPON) {
			$message = array(
				'text' => 'momentalne pouzivas inu zbran, nemozes vylozit dalsiu, kym ju neodhodis',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {

	}
}

?>