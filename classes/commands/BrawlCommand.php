<?php

class BrawlCommand extends Command {

	protected $place = 'hand';
	
	const OK = 1;
	
	const ADDITIONAL_CARD_NOT_IN_HAND = 2;
	
	const YOU_HAVE_TO_USE_ADDITIONAL_CARD = 3;
	
	protected $playersAndCards = array();
	
	protected function check() {
		if ($this->params[0]) {
			$additionalCardTitle = $this->params[0];
			$method = 'getHas' . ucfirst($additionalCardTitle) . 'OnHand';
			$additionalCard = $this->actualPlayer->$method();

			if ($additionalCard) {
				$this->cards[] = $additionalCard;
				$playerList = explode(';', $this->params[1]);
				$playerCardList = array();
				foreach ($playerList as $player) {
					$playerCard = explode('-', $player);
					$playerCardList[intval($playerCard[0])] = intval($playerCard[1]);
				}
				
				foreach ($this->getPlayers() as $player) {
					if ($player['id'] != $this->actualPlayer['id'] && $player['actual_lifes'] > 0) {
						$card = NULL;
						$place = 'hand';
						if (isset($playerCardList[$player['id']]) && $playerCardList[$player['id']] != 0) {
							// ak mame vybranu nejaku kartu pre hraca, tak sa pozrieme ci ju ma na stole
							$place = 'table';
							$card = $player->getCardWithId($place, $playerCardList[$player['id']]);							
						}
						
						if (!$card) {
							$place = 'hand';
							// ak sme kartu nenasli, vezmeme jednu nahodne z ruky
							$card = $player->getCardWithId($place);
						}
						// moze sa stat ze hrac nema kartu ani na stole ani na ruke
						if ($card) {
							$data = array(
								'player' => $player,
								'place' => $place,
								'cards' => array($card),
							);
							$this->playersAndCards[$player['id']] = $data;
						}
					}
				}
				$this->check = self::OK;
			} else {
				$this->check = self::ADDITIONAL_CARD_NOT_IN_HAND;
			}
		} else {
			$this->check = self::YOU_HAVE_TO_USE_ADDITIONAL_CARD;
		}
	}

	protected function run() {
		if ($this->check == 1) {
			// vyhodime brawl a pridavnu kartu
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			$place = 'hand';
			// vyhodime vsetky karty hracov
			foreach ($this->playersAndCards as $playerAndCards) {
				GameUtils::throwCards($this->game, $playerAndCards['player'], $playerAndCards['cards'], $playerAndCards['place']);
				if ($playerAndCards['place'] == 'table') {
					$place = 'table';
				}
			}

			if ($place == 'table') {
				// kedze je mozne ze berieme aspon jednu modru kartu ktora ovplyvnuje vzdialenost, preratame maticu
				// ak to bude velmi pomale, budeme to robit len ak je medzi zobratymi kartami fakt takato karta
				$matrix = GameUtils::countMatrix($this->game);
				$this->game['distance_matrix'] = serialize($matrix);
				$this->game->save();
			}
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			// TODO doplnit v hlaske aj miesto odkial bola karta zobrata
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil Brawl na zrusenie kariet',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);

			$message = array(
				'text' => 'pouzil si Brawl na odobratie kariet',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::ADDITIONAL_CARD_NOT_IN_HAND) {
			$message = array(
				'text' => 'nemas na ruke "' . $this->params[1] . '"',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::YOU_HAVE_TO_USE_ADDITIONAL_CARD) {
			$message = array(
				'text' => 'Musis vyhodit este jednu kartu',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
		return '';
	}
}
?>