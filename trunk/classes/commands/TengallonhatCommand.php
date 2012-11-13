<?php

/**
 * TODO vytvorit triedu DefendCommand kde bude switch na rozne typy utokov ktorym sa hrac moze branit a podla toho typu sa bude nastavovat dalsi hrac a stav hry
 */

class TengallonhatCommand extends Command {

	const OK = 1;
	
	protected function check() {
		$this->check = self::OK;
	}
	
	protected function run() {
		if ($this->check == self::OK) {
			// TODO moved to Command constructor
			$interTurnReason = unserialize($this->game['inter_turn_reason']);
			$attackingPlayerId = $interTurnReason['from'];
			$playerRepository = new PlayerRepository();
			$attackingPlayer = $playerRepository->getOneById($attackingPlayerId);

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
			$notices = $this->actualPlayer->getNoticeList();
			if (isset($notices['barrel_used'])) {
				unset($notices['barrel_used']);
			}
			if (isset($notices['character_jourdonnais_used'])) {
				unset($notices['character_jourdonnais_used']);
			}
			$this->actualPlayer->setNoticeList($notices);
			$this->actualPlayer['phase'] = Player::PHASE_NONE;
			$this->actualPlayer['command_response'] = '';
			$this->actualPlayer->save();

			// TODO toto asi nebudeme moct nastavovat hned ako jeden hrac da missed - pretoze tu mozu byt aj multiutoky (gulomet, indiani)
			$attackingPlayer['phase'] = Player::PHASE_PLAY;
			$attackingPlayer->save();

			// TODO toto takisto nebudeme moct nastavovat hned kvoli multiutokom
			$this->game['inter_turn'] = 0;
			$this->game['inter_turn_reason'] = '';
			$this->game->save();
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil Gallon Hat na zachranu zivota',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si Gallon Hat na zachranu zivota',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
		;
	}
}

?>