<?php

class CardChecker extends Checker {

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
				return $precheck;
			}
		} else {
			$message = array(
				'localizeKey' => 'missing_checker_params',
			);

			$this->addMessage($message);
			return FALSE;
		}
	}

	protected function isPuttable() {
		foreach ($this->command->getCards() as $card) {
			if (!$card->getIsPuttable()) {
				$localizedParams = $this->command->getLocalizedParams();
				
				$message = array(
					'localizeKey' => 'card_is_not_puttable',
					'localizeParams' => array($localizedParams[0]),
				);

				$this->addMessage($message);
				return FALSE;
			}
		}
		return TRUE;
	}
}

?>