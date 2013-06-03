<?php

class AddAiPlayerCommand extends Command {
	
	const OK = 1;
	
	const TOO_MANY_PLAYERS = 2;
	
	protected function check() {
		if ($this->loggedUser['id'] == $this->game['creator']) {
			if (count($this->players) < 8) {	// TODO rozlisit podla toho ci sa hra zakladny bang alebo je tam aj dodge city
				$this->check = self::OK;
			} else {
				$this->check = self::TOO_MANY_PLAYERS;
			}
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			
			$colorRepository = new ColorRepository();
			$count = $colorRepository->getCountAll();
			$randomColor = mt_rand(1, $count);
			
			$userParams = array(
				'username' => 'AI-' . str_pad(mt_rand(0, 999), 3, 0, STR_PAD_LEFT),
				'color' => $randomColor,
			);
			
			$userRepository = new UserRepository();
			$newUser = $userRepository->getOneByUsername($userParams['username']);
			if (!$newUser) {
				$newUser = new User($userParams);
				$newUser = $newUser->save(TRUE);
			}
			
			$playersCount = GameUtils::getPosition($this->game);

			$strategyRepository = new AiStrategyRepository();
			$strategyRepository->addOrderBy(array('RAND()' => ''));
			$strategy = $strategyRepository->getOneBy();
			
			$params = array(
				'game' => $this->game['id'],
				'user' => $newUser['id'],
				'seat' => GameUtils::getSeatOnPosition($playersCount),
				'ai_strategy' => $strategy['id'],	// TODO get random strategy
			);
			
			$player = new Player($params);
			$player->save();
		}
	}
	
	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => 'AI hrac bol pridany do hry',
			);
			$this->addMessage($message);
		} elseif ($this->check == self::TOO_MANY_PLAYERS) {
			$message = array(
				'text' => 'Prilis vela hracov v hre',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}
	
	protected function createResponse() {
		
	}
}

?>