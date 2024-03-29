<?php

class ChooseCharacterCommand extends Command {

	const OK = 1;
	const NO_CHARACTER_CHOSEN = 2;
	const NOT_POSSIBLE_CHARACTER_CHOSEN = 3;

	protected function check() {
		if ($this->params['selectedCharacter']) {
			$possibleChoices = unserialize($this->actualPlayer['possible_choices']);
			$possibleCharacters = $possibleChoices['possible_characters'];
			if (in_array($this->params['selectedCharacter'], $possibleCharacters)) {
				$this->check = self::OK;
			} else {
				$this->check = self::NOT_POSSIBLE_CHARACTER_CHOSEN;
			}
		} else {
			$this->check = self::NO_CHARACTER_CHOSEN;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			$this->actualPlayer['charakter'] = intval($this->params['selectedCharacter']);
			$this->actualPlayer['command_response'] = '';
			$this->actualPlayer['possible_choices'] = '';
			$this->actualPlayer->save();

			$this->game = $this->game->save(TRUE);
		}
	}

	protected function generateMessages() {
		$messages = array();
		if ($this->check == self::OK) {
			
		}
		return $messages;
	}

	protected function createResponse() {
		$playerRepository = new PlayerRepository();
		$count = $playerRepository->getCountByGameAndCharakter($this->game['id'], 0);
		
		if ($count === 0) {
			return Command::setup('command=start', $this->game);
		}
		return '';
	}
}

?>