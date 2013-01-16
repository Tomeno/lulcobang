<?php

/**
 * class for checking the player's phase
 *
 * @author Michal Lulco <michal.lulco@gmail.com>
 */
class PlayerPhaseChecker extends Checker {

	/**
	 * main check method
	 *
	 * @return	boolean
	 */
	public function check() {
		if ($this->precheckerParams && is_array($this->precheckerParams)) {
			$precheck = TRUE;
			foreach ($this->precheckerParams as $precheckMethod) {
				$precheck = $this->$precheckMethod();
				if ($precheck === FALSE) {
					break;
				}
			}
			return $precheck;
		} else {
			$message = array(
				'localizeKey' => 'missing_checker_params',
			);

			$this->addMessage($message);
			return FALSE;
		}
	}

	protected function getPhaseMessage($checkingPhase) {
		$actualPlayer = $this->command->getActualPlayer();
		$message = array();
		switch ($actualPlayer['phase']) {
			case Player::PHASE_NONE:
				$message = array(
					'localizeKey' => 'not_your_turn',
				);
				break;
			case Player::PHASE_DRAW_HIGH_NOON:
				$message = array(
					'localizeKey' => 'draw_predraw_first',
				);
				break;
			case Player::PHASE_DYNAMITE:
				$message = array(
					'localizeKey' => 'draw_dynamite_first',
				);
				break;
			case Player::PHASE_JAIL:
				$message = array(
					'localizeKey' => 'draw_jail_first',
				);
				break;
			case Player::PHASE_DRAW:
				$message = array(
					'localizeKey' => 'draw_cards_first',
				);
				break;
			case Player::PHASE_PLAY:
				$message = array(
					'localizeKey' => 'play_and_throw_cards',
				);
				break;
			case Player::PHASE_UNDER_ATTACK:
				$message = array(
					'localizeKey' => 'you_are_under_attack_use_defensive_cards',
				);
				break;
			case Player::PHASE_WAITING:
				$message = array(
					'localizeKey' => 'you_have_to_wait',
				);
		}
		if ($message) {
			$this->addMessage($message);
		}
	}

	/**
	 * checks if player is in draw phase
	 *
	 *
	 * @return	boolean
	 */
	protected function isInDrawPhase() {
		$actualPlayer = $this->command->getActualPlayer();
		if ($actualPlayer['phase'] === Player::PHASE_DRAW) {
			return TRUE;
		} else {
			$this->getPhaseMessage('draw');
			return FALSE;
		}
	}

	/**
	 * checks if player is in play phase
	 *
	 * @return	boolean
	 */
	protected function isInPlayPhase() {
		$actualPlayer = $this->command->getActualPlayer();
		if ($actualPlayer['phase'] == Player::PHASE_PLAY) {
			return TRUE;
		} else {
			$this->getPhaseMessage('play');
			return FALSE;
		}
	}

	protected function isUnderAttack() {
		$actualPlayer = $this->command->getActualPlayer();
		if ($actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
			return TRUE;
		} else {
			$this->getPhaseMessage('under_attack');
			return FALSE;
		}
	}
}

?>