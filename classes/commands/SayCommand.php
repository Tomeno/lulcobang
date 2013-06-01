<?php

class SayCommand extends Command {

	const OK = 1;
	const ALREADY_JOINED = 2;
	const GAME_STARTED = 3;
	const NO_GAME = 4;

	protected function check() {
		$this->check = self::OK;
	}

	protected function run() {
		if ($this->check === self::OK) {
			$messageParams = array(
				'text' => $this->params['text'],
				'room' => $this->room['id'],
				'game' => $this->game['id'],
			);
			Chat::addMessage($messageParams);
			if ($this->game->getIsHNGag()) {
				if ($this->actualPlayer['actual_lifes'] == 1) {
					// TODO nejaky priznak ze hrac bol upozorneny a potom mu toto uz nezobrazovat len ho removnut z hry
					$message = array(
						'text' => 'nesmies hovorit, lebo stratis posledny zivot',
						'toUser' => $this->loggedUser['id'],
					);
					$this->addMessage($message);
				} else {
					// TODO info o tom ze hrac stratil zivot kvoli tomu ze keca
					$this->actualPlayer['actual_lifes'] = $this->actualPlayer['actual_lifes'] - 1;
					$this->actualPlayer->save();
				}
			}
		}
	}

	protected function generateMessages() {
		
	}

	protected function createResponse() {
		return '';
	}
}

?>