<?php

class GameBox extends AbstractBox {
	
	protected $template = 'game.tpl';
	
	protected $game = NULL;

	protected function setup() {
		$loggedUser = LoggedUser::whoIsLogged();
		MySmarty::assign('loggedUser', $loggedUser);

		if (Utils::post('create')) {
			Command::execute('.create', $this->game);

			$roomRepository = new RoomRepository();
			$room = $roomRepository->getOneByAlias(Utils::get('identifier'));

			$gameRepository = new GameRepository();
			$this->game = $gameRepository->getOneByRoom($room['id']);
		} elseif (Utils::post('join')) {
			Command::execute('.join', $this->game);
		} elseif (Utils::post('start')) {
			Command::execute('.start', $this->game);
		}
		
		if ($this->game) {
			MySmarty::assign('game', $this->game);
			if ($this->game['status'] == Game::GAME_STATUS_CREATED) {
				MySmarty::assign('joinGame', Localize::getMessage('join_game'));

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