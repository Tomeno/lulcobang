<?php

class PassCommand extends Command {

	const OK = 1;

	const TOO_MANY_CARDS = 2;

	protected function check() {

		// TODO ak je na rade a uz tahal a este nejake specialne charaktery
		if ($this->actualPlayer['actual_lifes'] >= count($this->actualPlayer->getHandCards())) {
			$this->check = self::OK;
		} else {
			$this->check = self::TOO_MANY_CARDS;
		}
	}
	protected function run() {
		if ($this->check == self::OK) {
			$this->actualPlayer['phase'] = Player::PHASE_PREDRAW;
			$this->actualPlayer->save();

			// TODO next player check if is sheriff - phase predraw, if has dynamite and/or jail - phase blue cards, else phase draw

			// TODO dat to priamo do triedy Game
			$nextPosition = GameUtils::getNextPosition($this->game);
			$this->game['turn'] = $nextPosition;
			$this->game->save();
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