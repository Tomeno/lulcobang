<?php

class NormalStrategy extends AbstractStrategy {
	
	// copied from passive strategy - create own method
	// something we need to change, but I don't know what now
	// maybr first attack someone and than put cards to the table - or put blue cards and then attack and after this put green cards
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