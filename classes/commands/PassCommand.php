<?php

class PassCommand extends Command {

	const OK = 1;

	const TOO_MANY_CARDS = 2;

	const NO_GAME = 3;

	const NOT_YOUR_TURN = 4;

	const BAD_PHASE = 5;

	protected function check() {
		if ($this->game && $this->game['status'] == Game::GAME_STATUS_STARTED) {
			$playerOnTurn = $this->game->getPlayerOnTurn();
			if ($playerOnTurn['id'] == $this->actualPlayer['id']) {
				if ($this->actualPlayer['phase'] == Player::PHASE_PLAY) {
					$handCardsCount = count($this->actualPlayer->getHandCards());
					if (($this->actualPlayer['actual_lifes'] >= $handCardsCount) || ($handCardsCount <= 10 && $this->actualPlayer->getCharacter()->getIsSeanMallory())) {
						$this->check = self::OK;
					} else {
						$this->check = self::TOO_MANY_CARDS;
					}
				} else {
					$this->check = self::BAD_PHASE;
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
			$this->actualPlayer['phase'] = Player::PHASE_NONE;
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
			$this->messages[] = array(
				'user' => User::SYSTEM,
				'notToUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'player_pass_turn',
				'localizeParams' => array($this->loggedUser['username']),
			);

			$this->messages[] = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'you_pass_turn',
			);
		} elseif ($this->check == self::TOO_MANY_CARDS) {
			$this->messages[] = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'cannot_pass_too_many_cards_on_hand',
			);
		} 
	}

	protected function createResponse() {

	}
}

?>