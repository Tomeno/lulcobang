<?php

abstract class DefensiveCommand extends Command {

	const OK = 1;
	
	const CANNOT_PLAY_CARD = 2;
	
	const YOU_ARE_UNDER_INDIANS_ATTACK = 3;
	
	const YOU_ARE_UNDER_DUEL_ATTACK = 4;
	
	protected function check() {
		if ($this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
			if ($this->interTurnReason['action'] == 'indians') {
				$this->check = self::YOU_ARE_UNDER_INDIANS_ATTACK;
			} elseif ($this->interTurnReason['action'] == 'duel') {
				$this->check = self::YOU_ARE_UNDER_DUEL_ATTACK;
			} else {
				// proti Belle Star sa nedaju pouzit karty vylozene na stole
				$attackingPlayerNotices = $this->attackingPlayer->getNoticeList();
				if ($attackingPlayerNotices['character_used'] && $this->attackingPlayer->getIsBelleStar()) {
					$attackingUser = $this->attackingPlayer->getUser();
					$card = $this->cards[0];
					// vedla a uhyb sa daju pouzit, preto tu je podmienka ze karta musi byt zelena
					if ($card->getIsGreen()) {
						$message = array(
							'toUser' => $this->loggedUser['id'],
							'text' => 'Nemozes pouzit ' . $card->getTitle() . ' proti ' . $attackingUser['username'],
						);
						$this->addMessage($message);
					} else {
						$this->check = self::OK;
					}
				} else {
					$this->check = self::OK;
				}
			}
		} else {
			$this->check = self::CANNOT_PLAY_CARD;
		}
	}
}

?>