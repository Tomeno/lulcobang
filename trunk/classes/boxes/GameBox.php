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

			$playerRepository = new PlayerRepository();
			$actualPlayer = $playerRepository->getOneByGameAndUser($this->game['id'], $loggedUser['id']);

			// phases when we want to make autoreload
			if (in_array($actualPlayer['phase'], array(Player::PHASE_NONE, Player::PHASE_WAITING))) {
				MySmarty::assign('refreshGameBox', TRUE);
			}

			MySmarty::assign('response', $actualPlayer['command_response']);

			if ($this->game['status'] == Game::GAME_STATUS_CREATED) {
				if (!GameUtils::checkUserInGame($loggedUser, $this->game)) {
					MySmarty::assign('joinGameAvailable', TRUE);
				} elseif ($loggedUser['id'] == $this->game['creator']) {
					MySmarty::assign('startGameAvailable', Localize::getMessage('start_game'));
				}
			}
		} else {
			MySmarty::assign('createGameAvailable', Localize::getMessage('create_game'));
		}
	}

	public function setGame(Game $game) {
		$this->game = $game;
	}
}

?>