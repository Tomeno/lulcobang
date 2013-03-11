<?php

class GameBox extends AbstractBox {
	
	protected $template = 'game.tpl';
	
	protected $game = NULL;
	
	protected $room = NULL;

	protected function setup() {
		$loggedUser = LoggedUser::whoIsLogged();
		MySmarty::assign('loggedUser', $loggedUser);
		MySmarty::assign('room', $this->room);
		
		if ($this->game) {
			MySmarty::assign('game', $this->game);
			MySmarty::assign('gameStartedStatus', Game::GAME_STATUS_STARTED);

			$playerRepository = new PlayerRepository();
			$actualPlayer = $playerRepository->getOneByGameAndUser($this->game['id'], $loggedUser['id']);

			// phases when we want to make autoreload
			
			$refreshGameBox = FALSE;
			if (in_array($actualPlayer['phase'], array(Player::PHASE_NONE, Player::PHASE_WAITING))) {
				if ($this->game['status'] == Game::GAME_STATUS_INITIALIZED && $actualPlayer['possible_choices'] != '') {
					$refreshGameBox = FALSE;
				} else {
					$refreshGameBox = TRUE;
				}
				
			}
			MySmarty::assign('refreshGameBox', $refreshGameBox);
			
			// zobrazime len hracovi ktory je na tahu resp. v medzitahu
			$playerOnMove = $this->game->getPlayerOnMove();
			if ($playerOnMove['id'] == $actualPlayer['id'] || $this->game['status'] == Game::GAME_STATUS_INITIALIZED) {
				MySmarty::assign('response', $actualPlayer['command_response']);
			}

			if ($this->game['status'] == Game::GAME_STATUS_CREATED) {
				if (!GameUtils::checkUserInGame($loggedUser, $this->game)) {
					MySmarty::assign('joinGameAvailable', TRUE);
				} elseif ($loggedUser['id'] == $this->game['creator']) {
					MySmarty::assign('startGameAvailable', Localize::getMessage('start_game'));
				}
			} elseif ($this->game['status'] == Game::GAME_STATUS_ENDED) {
				MySmarty::assign('createGameAvailable', Localize::getMessage('create_game'));
				MySmarty::assign('refreshGameBox', TRUE);
			}
		} else {
			MySmarty::assign('createGameAvailable', Localize::getMessage('create_game'));
			MySmarty::assign('refreshGameBox', TRUE);
		}
	}

	public function setGame(Game $game) {
		$this->game = $game;
	}
	
	public function setRoom(Room $room) {
		$this->room = $room;
	}
}

?>