<?php

/**
 * class for passive strategy
 * 
 * hraci s touto strategiou len tahaju karty, vykladaju modre a zelene karty, brania sa utokom
 * a odhadzuju prebytocne karty, neutocia na inych
 * 
 * Nie je to realna strategia, ktora by sa dala pouzit, sluzi len ako prvy pokus o vytvorenie strategie
 */
class PassiveStrategy extends AbstractStrategy {
	
	protected function playCards() {
		$command = $this->drawExtraCards();
		if (!$command) {
			$command = $this->putCardsToTheTable();
			
			if (!$command) {
				$command = $this->addLives();

				// ak som nenasiel co by som mohol spravit, tak vyhodim karty a posuniem tah
				if (!$command) {
					$command = $this->throwExtraCards();

					if (!$command) {
						$command = 'command=pass';
					}
				}
			}
		}
		return $command;
	}
}

?>