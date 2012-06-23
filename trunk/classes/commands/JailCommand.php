<?php

class JailCommand extends Command {
	
	protected $attackedPlayer = NULL;

	protected function check() {
		// TODO checker ci uz nema napadnuty hrac jail na stole
	}

	protected function run() {
		// TODO create as checker
		$attackedPlayer = $this->params[0];
		foreach ($this->players as $player) {
			$user = $player->getUser();
			if ($user['username'] == $attackedPlayer) {
				$this->attackedPlayer = $player;
				break;
			}
		}

		if ($this->attackedPlayer) {
			GameUtils::moveCards($this->game, $this->cards, $this->actualPlayer, 'table', $this->attackedPlayer);
		}
	}

	protected function generateMessages() {
	}

	protected function createResponse() {

	}
}

?>