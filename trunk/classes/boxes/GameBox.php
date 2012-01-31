<?php

class GameBox extends AbstractBox {
	
	protected $template = 'game.tpl';
	
	protected $game = NULL;

	protected function setup() {
		$loggedUser = LoggedUser::whoIsLogged();
		MySmarty::assign('loggedUser', $loggedUser);
		
		if ($this->game) {
			MySmarty::assign('game', $this->game);
			MySmarty::assign('gameStartedStatus', Game::GAME_STATUS_STARTED);

			if ($this->game['status'] == Game::GAME_STATUS_CREATED) {
				if (!GameUtils::checkUserInGame($loggedUser, $this->game)) {
					MySmarty::assign('joinGameAvailable', TRUE);
				}
				// TODO hru moze spustit len creator
				MySmarty::assign('startGame', Localize::getMessage('start_game'));
			}
		} else {
			MySmarty::assign('createGame', Localize::getMessage('create_game'));
		}
	}

	public function setGame(Game $game) {
		$this->game = $game;
	}
}

?>