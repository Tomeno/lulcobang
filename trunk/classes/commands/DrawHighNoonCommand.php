<?php

class DrawHighNoonCommand extends Command {
	
	const OK = 1;
	
	
	protected function check() {
		if ($this->game->getIsHighNoon()) {
			if ($this->game->getHighNoonPile()) {
				if ($this->actualPlayer->getRoleObject()->getIsSheriff()) {
					if ($this->actualPlayer['phase'] == Player::PHASE_DRAW_HIGH_NOON) {
						$this->check = self::OK;
					} else {
						// nie si vo faze tahania high noon
					}
				} else {
					// nie si serif
				}
				
			} else {
				// uz nemame karty v high noon baliku
			}
		} else {
			// nehrame high noon rozsirenie
		}
	}
	
	protected function run() {
		if ($this->check == 1) {
			$this->actualPlayer['phase'] = $this->getNextPhase($this->actualPlayer);
			$this->actualPlayer->save();
			
			$highNoonPile = unserialize($this->game['high_noon_pile']);
			$drawnCard = array_pop($highNoonPile);
			$this->game['high_noon'] = $drawnCard;
			$this->game['high_noon_pile'] = serialize($highNoonPile);
			$this->game = $this->game->save(TRUE);
			
			$this->executeSpecialAction();
			
			// TODO ked prichadza High noon, treba nastavovat fazy inak
			
			$matrix = GameUtils::countMatrix($this->game);
			$this->game['distance_matrix'] = serialize($matrix);
			$this->game->save();
		}
	}
	
	protected function executeSpecialAction() {
		if ($this->game->getIsHNTheDoctor()) {
			$this->theDoctor();
		} elseif ($this->game->getIsHNTheDaltons()) {
			$this->theDaltons();
		} elseif ($this->game->getIsHNHelenaZontero()) {
			$this->helenaZontero();
		}
	}
	
	protected function theDoctor() {
		$min = 100;
		foreach ($this->game->getPlayers() as $player) {
			if ($player['actual_lifes'] > 0) {
				if ($player['actual_lifes'] < $min) {
					$min = $player['actual_lifes'];
				}
			}
		}

		foreach ($this->game->getPlayers() as $player) {
			if ($player['actual_lifes'] > 0 && $player['actual_lifes'] == $min) {
				$player['actual_lifes'] = min($player['max_lifes'], $player['actual_lifes'] + 1);
				$player->save();

				// TODO messages
			}
		}
	}
	
	protected function theDaltons() {
		foreach ($this->game->getPlayers() as $player) {
			if ($player->getHasBlueOnTheTable()) {
				$tableCards = $player->getTableCards();
				$blueCards = array();
				foreach ($tableCards as $tableCard) {
					if ($tableCard->getIsBlue()) {
						$blueCards[] = $tableCard;
					}
				}
				
				if ($blueCards) {
					$randomCard = $blueCards[array_rand($blueCards)];
					// ak by bol problem s tym ze by sa prepisovala hra niekde dalej, tak throw cards vracia game a playera
					GameUtils::throwCards($this->game, $player, array($randomCard), 'table');
				}
			}
		}
	}
	
	protected function helenaZontero() {
		$drawnCardIds = GameUtils::drawCards($this->game, 1);
		$cardRepository = new CardRepository(TRUE);
		$drawnCard = $cardRepository->getOneById($drawnCardIds);
		
		if ($drawnCard->getIsRed($this->game)) {
			$roles = array();
			foreach ($this->getPlayers() as $player) {
				if (!$player->getRoleObject()->getIsSheriff() && $player->getIsAlive()) {
					$role = $player->getRoleObject();
					$roles[] = $role['id'];
				}
			}
			shuffle($roles);
			foreach ($this->getPlayers() as $newPlayer) {
				if (!$newPlayer->getRoleObject()->getIsSheriff() && $newPlayer->getIsAlive()) {
					$role = array_pop($roles);
					$newPlayer['role'] = $role;
					$newPlayer->save();
				}
			}
		}
		
		GameUtils::throwCards($this->game, NULL, array($drawnCard));
	}


	protected function generateMessages() {
		;
	}
	
	protected function createResponse() {
		;
	}
	
}

?>