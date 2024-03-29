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

			$cardBaseTypeRepository = new CardBaseTypeRepository();
			$validCardBaseTypes = $cardBaseTypeRepository->getByValid(1);

			$validCardBaseTypesIdList = array();
			foreach ($validCardBaseTypes as $cardBaseType) {
				$validCardBaseTypesIdList[] = $cardBaseType['id'];
			}

			$cardRepository = new CardRepository();
			$where = array(
				'column' => 'card_base_type',
				'value' => $validCardBaseTypesIdList
			);
			$cardRepository->addAdditionalWhere($where);
			
			$gameSets = unserialize($this->room['game_sets']);
			if ($gameSets) {
				$where = array(
					'column' => 'game_set',
					'value' => $gameSets,
				);
				$cardRepository->addAdditionalWhere($where);
			}
			
			$cards = $cardRepository->getCardIds();
			shuffle($cards);

			// TODO prepare High noon draw pile
			// TODO - other extensions
			
			$players = $this->players;
			foreach ($players as $player) {
				$playerCards = array();

				$character = $player->getAdditionalField('character');
				
				$role = $player->getAdditionalField('role');
				$player['actual_lifes'] = $character['lifes'];

				$cardsCount = $player->getIsBigSpencer() ? 5 : $player['actual_lifes'];
				for ($i = 0; $i < $cardsCount; $i++) {
					$playerCards[] = array_pop($cards);
				}

				if ($role['type'] == Role::SHERIFF) {
					$player['phase'] = Player::PHASE_DRAW;
					$player['actual_lifes'] = $player['actual_lifes'] + 1;
				}
				$player['max_lifes'] = $player['actual_lifes'];

				$player['hand_cards'] = serialize($playerCards);
				$player['table_cards'] = serialize(array());
				$player['wait_cards'] = serialize(array());

				$player->save();
			}

			$this->game['draw_pile'] = serialize($cards);
			$this->game['throw_pile'] = serialize(array());
			// musime ulozit hru lebo hracom sa zmenili charaktery
			$this->game = $this->game->save(TRUE);

			$this->game = GameUtils::changePositions($this->game);

			foreach ($this->game->getPlayers() as $player) {
				if ($player['role'] == Role::SHERIFF) {
					$this->game['turn'] = $player['id'];
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