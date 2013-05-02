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
			$precheck = TRUE;
			$game = $this->command->getGame();

			if ($this->command->getUseCharacter() === TRUE && $actualPlayer->getIsDocHolyday($game)) {
				// v pripade doca holydaya spravime celu kontrolu v prikaze throw
				return TRUE;
			}
			
			foreach ($this->precheckerParams as $checkingMethod) {
				$negation = FALSE;

				if (substr($checkingMethod, 0, 7) === '!getHas') {
					$checkingMethod = str_replace('!', '', $checkingMethod);
					$negation = TRUE;
				}
				
				$cardName = $params['playCardName'];
				$checkingMethod = str_replace('###CARD_PLACEHOLDER###', ucfirst($cardName), $checkingMethod);
				
				// pre calamity janet prehodime karty bang a missed ak sa pouziva charakter
				if ($this->command->getUseCharacter() == TRUE && $actualPlayer->getIsCalamityJanet($game)) {
					if ($checkingMethod == 'getHasMissedOnHand') {
						$checkingMethod = 'getHasBangOnHand';
					} elseif ($checkingMethod == 'getHasBangOnHand') {
						$checkingMethod = 'getHasMissedOnHand';
					}
				}

				$placeParam = $params['place'];
				if ($placeParam == 'table') {
					$place = 'OnTheTable';
				} elseif ($placeParam == 'wait') {
					$place = 'OnWait';
				} else {
					$place = 'OnHand';
				}
				$checkingMethod = str_replace('###PLACE_PLACEHOLDER###', $place, $checkingMethod);

				if ($this->command->getUseCharacter() == TRUE && $actualPlayer->getIsElenaFuente($game)) {
					$checkingMethod = 'getHas' . ucfirst($cardName) . 'OnHand';
				}
				
				$card = $actualPlayer->$checkingMethod($game);
				if ($card) {
					if ($negation === FALSE) {
						if (strpos($checkingMethod, 'OnTheTable')) {
							$usePlace = 'table';
						} elseif (strpos($checkingMethod, 'OnHand')) {
							$usePlace = 'hand';
						} elseif (strpos($checkingMethod, 'OnWait')) {
							$usePlace = 'wait';
						}
						
						$card = $actualPlayer->getCardWithId($usePlace, $params['playCardId']);
						// dame si kartu do pola kariet aby sme s nou vedeli potom dalej robit
						if ($card) {
							$this->command->addCard($card);
							$precheck = TRUE;
						} else {
							echo 'false';
							// TODO nemas kartu s tymto ideckom
							$precheck = FALSE;
							break;
						}
					} else {
						$message = array(
							'localizeKey' => 'you_have_card_on_table',	// toto tu by sa malo menit podla miesta, nie stale table
							'localizeParams' => array($cardName),
						);
						$this->addMessage($message);
						
						$precheck = FALSE;
						break;
					}
				} else {
					if ($negation === FALSE) {
						$message = array(
							'localizeKey' => 'do_not_have_card',
							'localizeParams' => array($cardName),
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