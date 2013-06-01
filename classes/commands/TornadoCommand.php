<?php

class TornadoCommand extends Command {
	const OK = 1;
	
	protected function check() {
		$this->check = self::OK;
	}

	protected function run() {
		if ($this->check == self::OK) {
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			
			$tornadoCards = array();
			foreach ($this->game->getAlivePlayers() as $player) {
				// najprv od kazdeho hraca vezme dve random karty
				$randomCards = array();
				$handCards = unserialize($player['hand_cards']);
				if (count($handCards) <= 2) {
					$randomCards = $handCards;
					$player['hand_cards'] = serialize(array());
				} else {
					shuffle($handCards);
					for ($i = 0; $i < 2; ++$i) {
						$randomCards[] = array_pop($handCards);
					}
					$player['hand_cards'] = serialize($handCards);
				}
				$player->save();
				
				// najdeme hraca na nasledujucej pozicii
				$nextPositionPlayer = $this->getNextPositionPlayer($this->game, $player);
				$tornadoCards[$nextPositionPlayer['id']] = $randomCards;
			}
			// kedze sa hraci zmenili musime znovu nacitat hru s novymi hracskymi detailami
			$this->game = $this->game->save(TRUE);
			foreach ($this->game->getAlivePlayers() as $player) {
				$handCards = unserialize($player['hand_cards']);
				if (isset($tornadoCards[$player['id']])) {
					$newHandCards = array_merge($handCards, $tornadoCards[$player['id']]);
					$player['hand_cards'] = serialize($newHandCards);
					$player->save();
				}
			}
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => 'Prislo tornado a kazdy hrac ma zrazu ine karty',
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
	}
}

?>