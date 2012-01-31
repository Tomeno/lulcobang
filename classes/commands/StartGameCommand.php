<?php

class StartGameCommand extends Command {

	const OK = 1;
	const ALREADY_STARTED = 2;
	const NOT_YET_INITIALIZED = 3;
	const SOME_PLAYERS_WITHOUT_CHARACTER = 4;
	const NO_GAME = 5;
	const SOMETHING_ELSE = 6;

	protected function check() {
		if ($this->game && $this->game['status'] != Game::GAME_STATUS_ENDED) {
			if ($this->game['status'] == Game::GAME_STATUS_INITIALIZED) {
				$playerRepository = new PlayerRepository();
				$count = $playerRepository->getCountByGameAndCharakter($this->game['id'], 0);
				if ($count === 0) {
					$this->check = self::OK;
				} else {
					$this->check = self::SOME_PLAYERS_WITHOUT_CHARACTER;
				}
			} elseif ($this->game['status'] == Game::GAME_STATUS_CREATED) {
				$this->check = self::NOT_YET_INITIALIZED;
			} elseif ($this->game['status'] == Game::GAME_STATUS_STARTED) {
				$this->check = self::ALREADY_STARTED;
			} else {
				$this->check = self::SOMETHING_ELSE;
			}
		} else {
			$this->check = self::NO_GAME;
		}
	}
	protected function run() {
		if ($this->check == self::OK) {
			$cardRepository = new CardRepository();

			$players = $this->players;

			$cards = $cardRepository->getCardIds();
			shuffle($cards);

			foreach ($players as $player) {
				$playerCards = array();

				$character = $player->getAdditionalField('character');
				
				$role = $player->getAdditionalField('role');
				$player['actual_lifes'] = $character['lifes'];

				for ($i = 0; $i < $player['actual_lifes']; $i++) {
					$playerCards[] = array_pop($cards);
				}

				if ($role['type'] == Role::SHERIFF) {
					$player['phase'] = Player::PHASE_DRAW;
					$player['actual_lifes'] = $player['actual_lifes'] + 1;
				}

				$player['hand_cards'] = serialize($playerCards);
				$player['table_cards'] = serialize(array());

				$player->save();
			}

			$this->game['draw_pile'] = serialize($cards);
			$this->game['throw_pile'] = serialize(array());
			// musime ulozit hru lebo hracom sa zmenili charaktery
			$this->game = $this->game->save(TRUE);

			$this->game = GameUtils::changePositions($this->game);

			// TODO daco je tu zle 
			foreach ($this->game->getPlayers() as $player) {
				if ($player['role'] == Role::SHERIFF) {
					$this->game['turn'] = $player['position'];
					break;
				}
			}

			$this->game['game_start'] = time();
			$this->game['status'] = Game::GAME_STATUS_STARTED;
			$matrix = GameUtils::countMatrix($this->game);
			$this->game['distance_matrix'] = serialize($matrix);
			$this->game = $this->game->save(TRUE);
		}
	}
	
	protected function generateMessages() {
		
	}

	protected function createResponse() {
		return '';
	}
}

?>