<?php

class StrategyInstancer {
	
	public static function instance(Player $player, Game $game) {
		$strategyRepository = new AiStrategyRepository();
		$strategy = $strategyRepository->getOneById($player['ai_strategy']);
		$strategyClassName = $strategy['title'];
		
		$role = $player->getRoleObject();
		if ($strategyClassName == 'Passive') {
			if ($role->getIsSheriff()) {
				// 2 players alive in game
				// or only bandits against me
				// or $me->getActualLives() <= $me->getMaxLives() / 2
				$strategyClassName = 'Normal';
			} elseif ($role->getIsRenegard()) {
				// 2 players alive in game
				// $game->getSheriff()->getActualLives() <= $game->getSheriff()->getMaxLives() / 2
				// or $me->getActualLives() <= $me->getMaxLives() / 2
				$strategyClassName = 'Normal';
			} elseif ($role->getIsBandit()) {
				// $game->getSheriff()->getActualLives() <= $game->getSheriff()->getMaxLives() / 2
				// or $me->getActualLives() <= $me->getMaxLives() / 2
				$strategyClassName = 'Normal';
			} elseif ($role->getIsVice()) {
				// $game->getSheriff()->getActualLives() <= $game->getSheriff()->getMaxLives() / 2
				// or $me->getActualLives() <= $me->getMaxLives() / 2
				$strategyClassName = 'Normal';
			}
		}
		
		
		$className = $strategyClassName . $role['title'] . 'Strategy';
		return new $className($player, $game);
	}
}

?>