<?php

class LifeCommand extends Command {
	protected function check() {
		;
	}

	protected function run() {
		$interTurnReason = unserialize($this->game['inter_turn_reason']);

		$attackingPlayerId = $interTurnReason['from'];
		$playerRepository = new PlayerRepository();
		$attackingPlayer = $playerRepository->getOneById($attackingPlayerId);

		$newLifes = $this->actualPlayer['actual_lifes'] - 1;
		if ($newLifes == 0) {
			// TODO message ze hrac zomrel
			// TODO ak v hre nie su mrtvozruti, odhodi uplne vsetky karty, inak si ich rozdelia tito hraci
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getHandCards(), 'hand');
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getTableCards(), 'table');
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getWaitCards(), 'wait');

			$role = $this->actualPlayer->getRoleObject();
		//	if ($role['id'] == Role::BANDIT) {
				// za banditu dostane utocnik 3 karty
				// TODO doplnit pocty ak su ine pre rozne charaktery utociacich hracov
				// TODO doplnit podmienky pre typy utokov ktorych sa tieto tahania tykaju - indiani tam myslim nepatria
				// TODO message o tom ze si tento hrac potiahol 3 karty za banditu
				$drawnCards = GameUtils::drawCards($this->game, 3);
				$handCards = unserialize($attackingPlayer['hand_cards']);
				foreach ($drawnCards as $card) {
					$handCards[] = $card;
				}
				$attackingPlayer['hand_cards'] = serialize($handCards);

				$this->game = GameUtils::changePositions($this->game);

				// TODO po zmene positions sa pravdepodobne zmeni aj pozicia hraca ktory je na tahu, treba to tu na tomto mieste znovu preratat a nastavit game[position] na poziciu hraca s ideckom ktore ma attacking player a rovnako aj inter_turn bude treba preratat
		//	}
		}
		$this->actualPlayer['actual_lifes'] = $newLifes;
		$this->actualPlayer['phase'] = Player::PHASE_NONE;
		$this->actualPlayer['command_response'] = '';
		$this->actualPlayer->save();

		// TODO toto asi nebudeme moct nastavovat hned ako jeden hrac da life - pretoze tu mozu byt aj multiutoky (gulomet, indiani)
		$attackingPlayer['phase'] = Player::PHASE_PLAY;
		$attackingPlayer->save();

		// TODO toto takisto nebudeme moct nastavovat hned kvoli multiutokom
		$this->game['inter_turn'] = 0;
		$this->game['inter_turn_reason'] = '';
		$this->game->save();
	}

	protected function generateMessages() {
		;
	}

	protected function createResponse() {
		;
	}
}

?>