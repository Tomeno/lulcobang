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

			if ($this->command->getUseCharacter() === TRUE && $actualPlayer->getIsDocHolyday()) {
				// v pripade doca holydaya spravime celu kontrolu v prikaze throw
				return TRUE;
			}
			
			foreach ($this->precheckerParams as $checkingMethod) {
				$cardIndex = 0;
				$placeIndex = 1;
				$negation = FALSE;

				if (substr($checkingMethod, 0, 7) === '!getHas') {
					$checkingMethod = str_replace('!', '', $checkingMethod);
					$negation = TRUE;
				}

				// TODO ked zahram catbalou alebo paniku a. i. - je to hned prvy parameter z prikazu, ktory sa ale sem nedostane, treba to domysliet tak aby sa to dostalo aj sem
				$cardName = $params[$cardIndex];
				$checkingMethod = str_replace('###CARD_PLACEHOLDER###', ucfirst($cardName), $checkingMethod);
				
				// pre calamity janet prehodime karty bang a missed ak sa pouziva charakter
				if ($this->command->getUseCharacter() == TRUE && $actualPlayer->getIsCalamityJanet()) {
					if ($checkingMethod == 'getHasMissedOnHand') {
						$checkingMethod = 'getHasBangOnHand';
					} elseif ($checkingMethod == 'getHasBangOnHand') {
						$checkingMethod = 'getHasMissedOnHand';
					}
				}

				$placeParam = $params[1];
				if ($placeParam == 'table') {
					$place = 'OnTheTable';
				} elseif ($placeParam == 'wait') {
					$place = 'OnWait';
				} else {
					$place = 'OnHand';
				}
				$checkingMethod = str_replace('###PLACE_PLACEHOLDER###', $place, $checkingMethod);

				if ($this->command->getUseCharacter() == TRUE && $actualPlayer->getIsElenaFuente()) {
					$checkingMethod = 'getHas' . ucfirst($cardName) . 'OnHand';
				}
				
				$card = $actualPlayer->$checkingMethod();
				
				if ($card) {
					if ($negation === FALSE) {
						// dame si kartu do pola kariet aby sme s nou vedeli potom dalej robit
						$this->command->addCard($card);
						$precheck = TRUE;
					} else {
						$message = array(
							'localizeKey' => 'you_have_card_on_table',	// toto tu by sa malo menit podla miesta, nie stale table
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
						$precheck = TRUE;
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