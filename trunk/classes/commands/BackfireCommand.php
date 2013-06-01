<?php

class BackfireCommand extends DefensiveCommand {
	
	// TODO checker - ak som pod utokom hufnice a gulometu nemozem zatial tuto kartu pouzit
	// asi by tam boli problemy s tym kto na koho striela a kto sa ma branit atd.
	
	protected function run() {
		if ($this->check == DefensiveCommand::OK) {
			// odhodime kartu dodge
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			$this->runMollyStarkAction();
			
			$this->actualPlayer['command_response'] = '';
			$this->actualPlayer['phase'] = Player::PHASE_NONE;
			$this->actualPlayer->save();
			
			$this->attackingPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
			$this->attackingPlayer->save();
			
			$this->game['inter_turn'] = $this->attackingPlayer['id'];
			$this->game['inter_turn_reason'] = serialize(array('action' => 'backfire', 'from' => $this->attackingPlayer['id'], 'to' => $this->attackingPlayer['id']));
			$this->game->save();
			
			// TODO vyriesit problem ked po obraneni sa proti backfire hrac nema nastavenu spravnu fazu
		}
	}

	protected function generateMessages() {
		if ($this->check == DefensiveCommand::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil backfire na zachranu zivota',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si backfire na zachranu zivota',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::CANNOT_PLAY_CARD) {
			$message = array(
				'text' => 'nemozes pouzit kartu backfire',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::YOU_ARE_UNDER_INDIANS_ATTACK) {
			$message = array(
				'text' => 'Proti utoku indianov nemozes pouzit backfire',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == DefensiveCommand::YOU_ARE_UNDER_DUEL_ATTACK) {
			$message = array(
				'text' => 'Proti duelu nemozes pouzit backfire',
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