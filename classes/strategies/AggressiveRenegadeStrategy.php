<?php

/**
 * trieda pre agresivneho odpadlika
 */
class AggressiveRenegadeStrategy extends AggressiveStrategy {
	
	/**
	 * vyber ciela pre utok
	 * vzdy ked moze, zautoci na niekoho, ak su v hre len dvaja, utoci na serifa
	 * 
	 * @param	array
	 * @return	Player|NULL
	 */
	protected function selectTarget($possibleTargets) {
		$target = NULL;
		$alivePlayers = $this->game->getAlivePlayers();
		if ($possibleTargets) {
			if ($alivePlayers == 2) {
				// pri dvoch hracoch moze utocit len na serifa, ktory bude na indexe 0
				return $possibleTargets[0];
			} else {
				// ak je viac zivych hracov, utocit na niekoho kto nie je serif
				foreach ($possibleTargets as $possibleTarget) {
					if (!$possibleTarget->getRoleObject()->getIsSheriff()) {
						return $possibleTarget;
					}
				}
			}
		}
		return $target;
	}
}

?>