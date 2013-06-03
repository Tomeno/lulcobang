<?php

class PassCommand extends Command {

	const OK = 1;

	const TOO_MANY_CARDS = 2;

	const NO_GAME = 3;

	const NOT_YOUR_TURN = 4;

	const BAD_PHASE = 5;

	protected function check() {
		if ($this->game && $this->game['status'] == Game::GAME_STATUS_STARTED) {
			$playerOnTurn = $this->game->getPlayerOnMove();
			if ($playerOnTurn['id'] == $this->actualPlayer['id']) {
				if ($this->actualPlayer['phase'] == Player::PHASE_PLAY) {
					$handCardsCount = count($this->actualPlayer->getHandCards());
					if ($this->actualPlayer['actual_lifes'] >= $handCardsCount) {
						$this->check = self::OK;
					} elseif ($handCardsCount <= 10 && $this->actualPlayer->getIsSeanMallory($this->game)) {
						$this->check = self::OK;
					} else {
						$this->check = self::TOO_MANY_CARDS;
					}
				} else {
					$this->check = self::BAD_PHASE;
				}
			} else {
				$this->check = self::NOT_YOUR_TURN;
			}
		} else {
			$this->check = self::NO_GAME;
		}
	}
	
	protected function run() {
		if ($this->check == self::OK) {
			$passTurn = TRUE;
			if ($this->game->getIsHNVendetta() && !$this->actualPlayer->getPlayedVendetta()) {
				// TODO mozno by toto mohla byt metoda
				$count = 1;
				// neviem ci lucky duke pri vendete taha dve karty
				if ($this->actualPlayer->getIsLuckyDuke($this->game)) {
					$count = 2;
				}
				
				$drawnCards = GameUtils::drawCards($this->game, $count);
				$isHeart = FALSE;
				$cardRepository = new CardRepository();
				$thrownCards = array();
				foreach ($drawnCards as $drawnCardId) {
					$drawnCard = $cardRepository->getOneById($drawnCardId);
					$thrownCards[] = $drawnCard;
					if ($drawnCard->getIsHearts($this->game)) {
						$isHeart = TRUE;
						// break tu nie je lebo musime prejst cez vsetky karty
						// aby sme vyrobili pole kariet ktore treba vyhodit
					}
				}
				GameUtils::throwCards($this->game, NULL, $thrownCards);
				
				if ($isHeart) {
					$passTurn = FALSE;
				}
			}
			
			if ($passTurn) {
				$this->actualPlayer['phase'] = Player::PHASE_NONE;
				$this->actualPlayer['bang_used'] = 0;
				$tableCards = unserialize($this->actualPlayer['table_cards']);
				$waitCards = unserialize($this->actualPlayer['wait_cards']);
				$this->actualPlayer['table_cards'] = serialize(array_merge($tableCards, $waitCards));
				$this->actualPlayer['wait_cards'] = serialize(array());
				// znulujeme notices
				$notices = $this->actualPlayer->getNoticeList();
				if (isset($notices['barrel_used'])) {
					unset($notices['barrel_used']);
				}
				if (isset($notices['character_jourdonnais_used'])) {
					unset($notices['character_jourdonnais_used']);
				}
				if (isset($notices['character_used'])) {
					unset($notices['character_used']);
				}
				// spravime zalohu premennej ghost
				$isGhost = $this->actualPlayer->getIsGhost();
				// zrusime ghosta
				if (isset($notices['ghost'])) {
					unset($notices['ghost']);
				}
				if (isset($notices['vendetta'])) {
					unset($notices['vendetta']);
				}
				if (isset($notices['blood_brothers'])) {
					unset($notices['blood_brothers']);
				}
				
				$this->actualPlayer->setNoticeList($notices);
				$this->actualPlayer = $this->actualPlayer->save(TRUE);

				if ($this->game->getIsHNGhostTown() && $isGhost === TRUE) {
					// odstranime ghosta
					$this->removePlayerFromGame();
				}

				$nextPositionPlayer = GameUtils::getPlayerOnNextPosition($this->game, $this->actualPlayer, TRUE);
				$this->game['turn'] = $nextPositionPlayer['id'];
				$this->game->save();

				if ($this->game->getIsHNGhostTown() && $nextPositionPlayer['actual_lifes'] == 0) {
					$notices = $nextPositionPlayer->getNoticeList();
					$notices['ghost'] = 1;
					$nextPositionPlayer->setNoticeList($notices);
				}

				$nextPositionPlayer['phase'] = $this->getNextPhase($nextPositionPlayer);
				$nextPositionPlayer->save();

				// preratame maticu
				$matrix = GameUtils::countMatrix($this->game);
				$this->game['distance_matrix'] = serialize($matrix);
				$this->game->save();
			} else {
				$notices = $this->actualPlayer->getNoticeList();
				$notices['vendetta'] = 1;
				$this->actualPlayer->setNoticeList($notices);
				$this->actualPlayer['phase'] = $this->getNextPhase($this->actualPlayer);
				$this->actualPlayer = $this->actualPlayer->save(TRUE);
				
				// TODO message
			}
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$this->messages[] = array(
				'user' => User::SYSTEM,
				'notToUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'player_pass_turn',
				'localizeParams' => array($this->loggedUser['username']),
			);

			$this->messages[] = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'you_pass_turn',
			);
		} elseif ($this->check == self::TOO_MANY_CARDS) {
			$this->messages[] = array(
				'user' => User::SYSTEM,
				'toUser' => $this->loggedUser['id'],
				'room' => $this->room['id'],
				'localizeKey' => 'cannot_pass_too_many_cards_on_hand',
			);
		} 
	}

	protected function createResponse() {

	}
}

?>