<?php

/**
 * checker for cards on hand or table for actual player
 *
 * @author Michal Lulco <michal.lulco@gmail.com>
 */
class ActualPlayerHasCardsChecker extends Checker {

	/**
	 * main method
	 *
	 * @return	boolean
	 */
	public function check() {
		if ($this->precheckerParams && is_array($this->precheckerParams)) {
			$actualPlayer = $this->command->getActualPlayer();
			$params = $this->command->getParams();
			$localizedParams = $this->command->getLocalizedParams();
			$precheck = TRUE;
			foreach ($this->precheckerParams as $checkingMethod) {
				$index = 0;
				$negation = FALSE;

				if (substr($checkingMethod, 0, 7) === '!getHas') {
					$checkingMethod = str_replace('!', '', $checkingMethod);
					$negation = TRUE;
				}

				$checkingMethod = str_replace('###PLACEHOLDER###', ucfirst($params[$index]), $checkingMethod);
				$card = $actualPlayer->$checkingMethod();
				
				if ($card) {
					if ($negation === FALSE) {
						// dame si kartu do pola kariet aby sme s nou vedeli potom dalej robit
						$this->command->addCard($card);
						$precheck = TRUE;
					} else {
						$message = array(
							'localizeKey' => 'you_have_card_on_table',
							'localizeParams' => array($localizedParams[$index]),
						);
						$this->addMessage($message);
						
						$precheck = FALSE;
						break;
					}
				} else {
					if ($negation === FALSE) {
						$message = array(
							'localizeKey' => 'do_not_have_card',
							'localizeParams' => array($localizedParams[$index]),
						);
						$this->addMessage($message);
						
						$precheck = FALSE;
						break;
					} else {
						return TRUE;
					}
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
}

?>