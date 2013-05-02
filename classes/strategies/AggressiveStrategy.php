<?php

class AggressiveStrategy extends AbstractStrategy {
	
	// copied from passive strategy - create own method
	// use attacking cards also
	protected function playCards() {
		$command = $this->drawExtraCards();
		if (!$command) {
			
			// TODO rozlozit na put attacking cards a put defensive (and green) cards -
			// aby pred utokom mali hraci na ruke co najviac kariet - aby to vyzeralo ze napr. na duel maju velku silu
			$command = $this->putCardsToTheTable();
			
			if (!$command) {
				$command = $this->attackPlayers();
				
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
		}
		return $command;
	}
	
	protected function attackPlayers() {
		$command = '';
		$bang = $this->player->getHasBangOnHand();
		if ($bang) {
			$bangLimit = 1;
			if ($this->game->getIsHNTheSermon()) {
				$bangLimit = 0;
			} elseif ($this->game->getIsHNShootout()) {
				$bangLimit = 2;
			}
			if ($this->player['bang_used'] < $bangLimit) {
				$target = $this->selectTarget($this->findPossibleTargets($this->player->getRange($this->game)));
				if ($target) {
					$user = $target->getUser();
					$command = 'command=bang&playCardId=' . $bang['id'] . '&playCardName=' . $bang->getCardName() . '&enemyPlayerId=' . $target['id'] . '&enemyPlayerUsername=' . $user['username'];
				}
			}
		}
		return $command;
	}
	
	protected function selectTarget($possibleTargets) {
		$target = NULL;
		if ($possibleTargets) {
			// vyberieme nahodneho hraca
			$target = $possibleTargets[array_rand($possibleTargets)];
		}
		return $target;
	}
}

?>