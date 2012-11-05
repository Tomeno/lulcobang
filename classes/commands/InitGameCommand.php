<?php

class InitGameCommand extends Command {
	
	const OK = 1;
	const ALREADY_INITIALIZED = 2;
	const ALREADY_STARTED = 3;
	const NOT_ENOUGH_PLAYERS = 4;
	const NO_GAME = 5;
	CONST NOT_ENOUGH_CHARACTERS = 6;

	protected $template = 'character-choice.tpl';

	protected function check() {
		if ($this->game && $this->game['status'] != Game::GAME_STATUS_ENDED) {
			$players = $this->players;

			if (count($players) >= Game::MINIMUM_PLAYERS_COUNT) {
				if ($this->game['status'] == Game::GAME_STATUS_INITIALIZED) {
					$this->check = self::ALREADY_INITIALIZED;
				} elseif ($this->game['status'] == Game::GAME_STATUS_STARTED) {
					$this->check = self::ALREADY_STARTED;
				} else {
					if (is_numeric($this->params[0])) {
						$characterRepository = new CharacterRepository();
						$charactersCount = $characterRepository->getCountAll();
						if ($charactersCount < (count($players) * $this->params[0])) {
							$this->check = self::NOT_ENOUGH_CHARACTERS;
						} else {
							$this->check = self::OK;
						}
					} else {
						$this->check = self::OK;
					}
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

			$characterCardsCount = is_numeric($this->params[0]) ? intval($this->params[0]) : 2;

			$players = $this->players;
			$roleRepository->setLimit(count($players));
			$roles = $roleRepository->getAll();
			shuffle($roles);

			$characters = $characterRepository->getByValid(1);
			shuffle($characters);

			$j = 0;
			foreach ($players as $player) {
				$playerPossibleCharacters = array();
				$player['role'] = $roles[$j]['id'];

				$index = $characterCardsCount * $j;
				for ($c = $index; $c < $index + $characterCardsCount; $c++) {
					$playerPossibleCharacters[] = $characters[$c]['id'];
				}

				$player['possible_choices'] = serialize(array('possible_characters' => $playerPossibleCharacters));

				$player->save();
				$j++;
			}

			$this->game['status'] = Game::GAME_STATUS_INITIALIZED;
			$this->game = $this->game->save(TRUE);
			
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => 'hra bola inicializovana',
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NOT_ENOUGH_PLAYERS) {
			$message = array(
				'text' => 'prilis malo hracov na zacatie hry',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
		if ($this->check == self::OK) {
			$actualPlayerResponse = '';
			foreach ($this->game->getAdditionalField('players') as $player) {
				$possibleChoices = unserialize($player['possible_choices']);
				$characterRepository = new CharacterRepository();
				$possibleCharacters = $characterRepository->getById($possibleChoices['possible_characters']);
				MySmarty::assign('possibleCharacters', $possibleCharacters);
				$response = MySmarty::fetch($this->template);
				$player['command_response'] = $response;
				$player->save();

				if ($player['id'] == $this->actualPlayer['id']) {
					$actualPlayerResponse = $response;
				}
			}
			return $actualPlayerResponse;
		} else {
			return '';
		}
	}
}

?>