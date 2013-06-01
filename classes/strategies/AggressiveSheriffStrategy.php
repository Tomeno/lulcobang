<?php

class AggressiveSheriffStrategy extends AggressiveStrategy {
	// nepouzit hromadnu zbran ak ma niekto 1 zivot a hra pomocnik
	
	/**
	 * vyber ciela pre utok
	 * vzdy ked moze, zautoci na niekoho, ak su v hre len dvaja, utoci na serifa
	 * 
	 * @param	array
	 * @return	Player|NULL
	 */
	protected function selectTarget($possibleTargets) {
		$target = NULL;
		if ($possibleTargets) {
			// TODO : najdeme najprv hracov ktori, na serifa strielali
			$aliveVicePlayers = $this->game->getAliveVicePlayers();
			// ak sme nikoho nenasli vyberieme hocikoho, kto nema 1 zivot aby sme nezabili nahodou pomocnika
			// ak pomocnik v hre nie je, tak je to jedno
			// TODO: mozno by si serif mal davat pozor aj na to ci nezabije odpadlika a nasledne ostane sam proti banditom
			foreach ($possibleTargets as $possibleTarget) {
				if ($possibleTarget['actual_lifes'] > 1 || count($aliveVicePlayers) == 0) {
					return $possibleTarget;
				}
			}
		}
		return $target;
	}
}

?>