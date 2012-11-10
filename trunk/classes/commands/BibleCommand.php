<?php

class BibleCommand extends Command {

	const OK = 1;
	
	const CANNOT_PLAY_CARD = 2;
	
	protected function check() {
		if ($this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
			$this->check = self::OK;
		} else {
			$this->check = self::CANNOT_PLAY_CARD;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			// TODO moved to Command constructor
			$interTurnReason = unserialize($this->game['inter_turn_reason']);
			$attackingPlayerId = $interTurnReason['from'];
			$playerRepository = new PlayerRepository();
			$attackingPlayer = $playerRepository->getOneById($attackingPlayerId);

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');

			$drawnCards = GameUtils::drawCards($this->game, 1);
			$handCards = unserialize($this->actualPlayer['hand_cards']);
			$handCards = array_merge($handCards, $drawnCards);
			$this->actualPlayer['hand_cards'] = serialize($handCards);
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
				'text' => $this->loggedUser['username'] . ' pouzil bibliu na zachranu zivota',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si bibliu na zachranu zivota',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::CANNOT_PLAY_CARD) {
			$message = array(
				'text' => 'nemozes pouzit kartu biblia',
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