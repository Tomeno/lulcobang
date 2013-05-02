<?php

class AggressiveOutlawStrategy extends AggressiveStrategy {
	
	/**
	 * vyber ciela pre utok
	 * vzdy ked moze, zautoci na serifa, ked nemoze radsej neutoci
	 * 
	 * @param	array
	 * @return	Player|NULL
	 */
	protected function selectTarget($possibleTargets) {
		$target = NULL;
		if ($possibleTargets) {
			foreach ($possibleTargets as $possibleTarget) {
				if ($possibleTarget->getRoleObject()->getIsSheriff()) {
					return $possibleTarget;
				}
			}
		}
		return $target;
	}
}

?>