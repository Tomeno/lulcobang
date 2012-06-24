<?php

class LifeCommand extends Command {
	protected function check() {
		;
	}

	protected function run() {

		$newLifes = $this->actualPlayer['actual_lifes'] - 1;
		$this->actualPlayer['actual_lifes'] = $newLifes;
		$this->actualPlayer->save();
		if ($newLifes == 0) {
			// TODO message ze hrac zomrel
			// TODO ak v hre nie su mrtvozruti, odhodi uplne vsetky karty, inak si ich rozdelia tito hraci
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getHandCards(), 'hand');
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getTableCards(), 'table');
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getWaitCards(), 'wait');

			$playerRepository = new PlayerRepository();

			$role = $this->actualPlayer->getRoleObject();
			if ($role['id'] == Role::BANDIT) {
				if ($playerRepository->getCountLivePlayersWithRoles($this->game['id'], array(Role::BANDIT, Role::RENEGARD)) == 0) {
					$this->endGame(array(Role::SHERIFF, Role::VICE));
				} else {

					// TODO doplnit pocty kariet ak su ine pre rozne charaktery utociacich hracov
					// TODO doplnit podmienky pre typy utokov ktorych sa tieto tahania tykaju - indiani tam myslim nepatria
					// TODO message o tom ze si tento hrac potiahol 3 karty za banditu

					// za banditu dostane utocnik 3 karty - ale len ak slo o priamy utok
					$drawnCards = GameUtils::drawCards($this->game, 3);
					$handCards = unserialize($this->attackingPlayer['hand_cards']);
					foreach ($drawnCards as $card) {
						$handCards[] = $card;
					}
					$this->attackingPlayer['hand_cards'] = serialize($handCards);
					$this->attackingPlayer = $this->attackingPlayer->save(TRUE);
				}
			} elseif ($role['id'] == Role::SHERIFF) {
				if ($playerRepository->getCountLivePlayersWithRoles($this->game['id'], array(Role::BANDIT, Role::VICE)) > 0) {
					$this->endGame(array(Role::BANDIT));
				} elseif ($playerRepository->getCountLivePlayersWithRoles($this->game['id']) == 1 &&
					$playerRepository->getCountLivePlayersWithRoles($this->game['id'], array(Role::RENEGARD)) == 1) {
					$this->endGame(array(Role::RENEGARD));
				} else {
					$this->endGame();
				}
			} elseif ($role['id'] == Role::RENEGARD) {
				if ($playerRepository->getCountLivePlayersWithRoles($this->game['id'], array(Role::BANDIT, Role::RENEGARD)) == 0) {
					$this->endGame(array(Role::SHERIFF, Role::VICE));
				}
			}

			// TODO po zmene positions sa pravdepodobne zmeni aj pozicia hraca ktory je na tahu, treba to tu na tomto mieste znovu preratat a nastavit game[position] na poziciu hraca s ideckom ktore ma attacking player a rovnako aj inter_turn bude treba preratat
			$this->game = GameUtils::changePositions($this->game);
		}
		
		// vytvorit nejaku osobitnu metodu na toto
		if (in_array($this->interTurnReason['action'], array('indians', 'gatling', 'howitzer'))) {
			$nextPosition = GameUtils::getNextPosition($this->game, $this->actualPlayer['position']);
			foreach ($this->players as $player) {
				if ($player['id'] == $this->actualPlayer['id']) {
					$this->actualPlayer['actual_lifes'] = $newLifes;
					$this->actualPlayer['phase'] = Player::PHASE_NONE;
					$this->actualPlayer['command_response'] = '';
					$this->actualPlayer->save();
				} else {
					if ($player['position'] == $nextPosition) {
						$nextPositionPlayer = $player;
						$player['phase'] = Player::PHASE_UNDER_ATTACK;
						$player->save();
					}
				}
			}

			if ($nextPosition == $this->attackingPlayer['position']) {
				$this->game['inter_turn_reason'] = '';
				$this->game['inter_turn'] = 0;

				$this->attackingPlayer['phase'] = Player::PHASE_PLAY;
				$this->attackingPlayer->save();
			} else {
				// nastavime interturn
				$this->game['inter_turn_reason'] = serialize(array('action' => 'indians', 'from' => $this->attackingPlayer['id'], 'to' => $nextPositionPlayer['id']));
				$this->game['inter_turn'] = $nextPosition;
			}

		} else {
			
			$this->actualPlayer['phase'] = Player::PHASE_NONE;
			$this->actualPlayer['command_response'] = '';
			$this->actualPlayer->save();

			$this->game['inter_turn_reason'] = '';
			$this->game['inter_turn'] = 0;

			$this->attackingPlayer['phase'] = Player::PHASE_PLAY;
			$this->attackingPlayer->save();
		}
		
		$this->game->save();
	}

	private function endGame($roles) {
		$message = array(
			'text' => 'vyhrali roles: ' . print_R($roles, TRUE),
			'user' => User::SYSTEM,
		);

		$this->addMessage($message);

		$this->game['status'] = Game::GAME_STATUS_ENDED;
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