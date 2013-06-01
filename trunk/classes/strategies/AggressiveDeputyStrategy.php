<?php

/**
 * trieda pre agresivneho pomocnika
 */
class AggressiveDeputyStrategy extends AggressiveStrategy {
	
	/**
	 * vyber ciela pre utok
	 * vzdy ked moze, zautoci na niekoho kto utocil na serifa alebo na hocikoho okrem serifa
	 * 
	 * @param	array
	 * @return	Player|NULL
	 */
	protected function selectTarget($possibleTargets) {
		$target = NULL;
		if ($possibleTargets) {
			// TODO : najdeme najprv hracov ktori, na serifa strielali
			// ked sme nenasli nikoho kto na serifa utocil, zautocime na hocikoho kto nie je serif
			foreach ($possibleTargets as $possibleTarget) {
				if (!$possibleTarget->getRoleObject()->getIsSheriff()) {
					return $possibleTarget;
				}
			}
		}
		return $target;
	}
}

?>