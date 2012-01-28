<?php

class StartGameCommand extends Command {

	const OK = 1;
	const ALREADY_STARTED = 2;
	const NOT_ENOUGH_PLAYERS = 3;
	const NO_GAME = 4;

	protected function check() {

		echo 'StartGameCommand::check() Vyrobit z tohoto init game, ktora ako parameter bude brat pocet charakterov ktore si moze hrac vybrat,
			nasledne spravit chosecharactercommand kde bude vracat html s obrazkami charakterov a ked uz budu vsetci hraci mat vybrate charaktery,
			zavola  sa startgame command';
		exit();


		if ($this->game || $this->game['status'] == Game::GAME_STATUS_ENDED) {
			$players = $this->game['players'];

			if (count($players) >= 2) {
				if ($this->game['status'] == Game::GAME_STATUS_STARTED) {
					$this->check = self::ALREADY_STARTED;
				} else {
					$this->check = self::OK;
				}
			} else {
				$this->check = self::NOT_ENOUGH_PLAYERS;
			}
		} else {
			$this->check = self::NO_GAME;
		}
	}
	protected function run() {
		if ($this->check == self::OK) {
			$roleRepository = new RoleRepository();
			$characterRepository = new CharacterRepository();
			$cardRepository = new CardRepository();

			$players = $this->game['players'];

			$roleRepository->setLimit(count($players));
			$roles = $roleRepository->getAll();
			shuffle($roles);

			$characters = $characterRepository->getAll();
			shuffle($characters);

			$cards = $cardRepository->getCardIds();
			shuffle($cards);

			$j = 0;
			foreach ($players as $player) {
				$playerCards = array();
				$params = array();

				$params['role'] = $roles[$j]['id'];

				$params['charakter'] = $characters[$j]['id'];
				$params['actual_lifes'] = $characters[$j]['lifes'];

				for ($i = 0; $i < $params['actual_lifes']; $i++) {
					$playerCards[] = array_pop($cards);
				}

				if ($roles[$j]['type'] == Role::SHERIFF) {
					$params['phase'] = 1;
					$params['actual_lifes']++;
				}

				$params['hand_cards'] = serialize($playerCards);
				$params['table_cards'] = serialize(array());
				DB::update('player', $params, 'id = ' . intval($player['id']));

				$j++;
			}

			$params = array(
				'draw_pile' => serialize($cards),
				'throw_pile' => serialize(array()),
				'game_start' => time(),
				'status' => Game::GAME_STATUS_STARTED,
			);

			DB::update('game', $params, 'id = ' . intval($this->game['id']));

			$gameRepository = new GameRepository();
			$game = $gameRepository->getOneById($this->game['id']);

			$game = GameUtils::changePositions($game);
			foreach ($game['players'] as $player) {
				if ($player['role']['id'] == Role::SHERIFF) {
					GameUtils::setTurn($game, $player['position']);
				}
			}
			GameUtils::countMatrix($game);
		}
	}
	
	protected function write() {

	}

	protected function createResponse() {
		// TODO create response
		return '';
	}
}

?>