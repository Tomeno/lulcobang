<?php

class LifeCommand extends Command {
	protected $beerCard = NULL;
	
	const OK = 1;
	
	const SAVE_LAST_LIFE = 2;
	
	const NOT_LAST_LIFE = 3;
	
	const NO_BEER_ON_HAND = 4;
	
	const ONLY_TWO_PLAYERS_IN_GAME = 5;
	
	protected function check() {
		// TODO skontrolovat ci uz hrac nie je na 0
		$livePlayers = 0;
		foreach ($this->getPlayers() as $player) {
			if ($player['actual_lifes'] > 0) {
				$livePlayers++;
			}
		}
		
		if ($this->params[0] == 'beer') {
			if ($livePlayers > 2) {
				if ($this->actualPlayer['actual_lifes'] == 1) {
					$this->beerCard = $this->actualPlayer->getHasBeerOnHand();
					if ($this->beerCard) {
						$this->check = self::SAVE_LAST_LIFE;
					} else {
						$this->check = self::NO_BEER_ON_HAND;
					}
				} else {
					$this->check = self::NOT_LAST_LIFE;
				}
			} else {
				$this->check = self::ONLY_TWO_PLAYERS_IN_GAME;
			}
		} else {
			// mozno bude treba pridat dalsie checkery
			$this->check = self::OK;
		}
	}

	protected function run() {
		
		// TODO ked behom indianov zomrie posledny hrac pred tym kto pouzil indianov, tak je to nejako naprd
		// pravdepodobne to cele suvisi s tym ze sa pooposuvali pozicie a teraz uz nikto nevie kde sedel
		// actual player uz ma medzicasom inu poziciu ako je v pamati
		
		if ($this->check == self::SAVE_LAST_LIFE) {
			GameUtils::throwCards($this->game, $this->actualPlayer, array($this->beerCard));
			
			if ($this->actualPlayer->getCharacter()->getIsTequilaJoe()) {
				$this->actualPlayer['actual_lifes'] + 1;	// tequila joe si posledny zivot zachrani a 1 si este prida
			}
		} elseif ($this->check == self::OK) {
			$newLifes = $this->actualPlayer['actual_lifes'] - 1;
			$notices = $this->actualPlayer->getNoticeList();
			if (isset($notices['barrel_used'])) {
				unset($notices['barrel_used']);
			}
			if (isset($notices['character_jourdonnais_used'])) {
				unset($notices['character_jourdonnais_used']);
			}
			$this->actualPlayer->setNoticeList($notices);
			$this->actualPlayer['actual_lifes'] = $newLifes;
			$this->actualPlayer = $this->actualPlayer->save(TRUE);
			if ($newLifes <= 0) {
				$this->actualPlayer['position'] = 0;
				// TODO message ze hrac zomrel
				
				// ak je v hre Vera Custer tak moze mat jeden z tychto charakterov
				// preto su vsetky premenne array a nie len Player
				$vultureSams = array();
				$gregDiggers = array();
				$herbHunters = array();
				foreach ($this->getPlayers() as $player) {
					// pozrieme sa na vsetkych hracov ktori este nie su mrtvi a ani nie su aktualny hrac (bohvie ako je on ulozeny v $this->players :)
					if ($player['actual_lifes'] > 0 && $this->actualPlayer['id'] != $player['id']) {
						if ($player->getCharacter()->getIsVultureSam()) {
							$vultureSams[] = $player;
						} elseif ($player->getCharacter()->getIsGregDigger()) {
							$gregDiggers[] = $player;
						} elseif ($player->getCharacter()->getIsHerbHunter()) {
							$herbHunters[] = $player;
						}
					}
				}
				
				// pridame vsetkym gregom diggerom 2 zivoty (resp. tolko kolko potrebuju)
				if ($gregDiggers) {
					foreach ($gregDiggers as $gregDigger) {
						$newLifes = min($gregDigger['actual_lifes'] + 2, $gregDigger['max_lifes']);
						$gregDigger['actual_lifes'] = $newLifes;
						$gregDigger->save();
					}
				}
				
				// potiahneme pre kazdeho herba huntera 2 karty
				if ($herbHunters) {
					foreach ($herbHunters as $herbHunter) {
						$drawnCards = GameUtils::drawCards($this->game, 2);
						$handCards = unserialize($herbHunter['hand_cards']);
						foreach ($drawnCards as $card) {
							$handCards[] = $card;
						}
						$herbHunter['hand_cards'] = serialize($handCards);
						$herbHunter->save();
					}
				}
				
				if ($vultureSams) {
					if (count($vultureSams) == 1) {
						$vultureSamPlayer = $vultureSams[0];
						$retVal = GameUtils::moveCards($this->game, $this->actualPlayer->getHandCards(), $this->actualPlayer, 'hand', $vultureSamPlayer, 'hand');
						$vultureSamPlayer = $retVal['playerTo'];
						$this->actualPlayer = $retVal['playerFrom'];
						$retVal = GameUtils::moveCards($this->game, $this->actualPlayer->getTableCards(), $this->actualPlayer, 'hand', $vultureSamPlayer, 'table');
						$vultureSamPlayer = $retVal['playerTo'];
						$this->actualPlayer = $retVal['playerFrom'];
						$retVal = GameUtils::moveCards($this->game, $this->actualPlayer->getWaitCards(), $this->actualPlayer, 'hand', $vultureSamPlayer, 'wait');
						$vultureSamPlayer = $retVal['playerTo'];
						$this->actualPlayer = $retVal['playerFrom'];
					} else {
						throw new Exception("More than one Vulture Sam in a game", 1352146582);
					}
				} else {
					GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getHandCards(), 'hand');
					GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getTableCards(), 'table');
					GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getWaitCards(), 'wait');
				}
				
				// TODO po zmene positions sa pravdepodobne zmeni aj pozicia hraca ktory
				// je na tahu, treba to tu na tomto mieste znovu preratat a nastavit game[position]
				// na poziciu hraca s ideckom ktore ma attacking player a rovnako aj inter_turn bude treba preratat
				$this->game = GameUtils::changePositions($this->game);
				$matrix = GameUtils::countMatrix($this->game);
				$this->game['distance_matrix'] = serialize($matrix);
				$this->game = $this->game->save(TRUE);
				
				// najst hraca ktory ma fazu != 0 a nastavit ho v hre ako hraca ktory je na tahu 
				
				// znovu nacitame z databazy utociaceho hraca ( pre istotu )
				$attackingPlayerId = $this->interTurnReason['from'];
				$playerRepository = new PlayerRepository();
				$this->attackingPlayer = $playerRepository->getOneById($attackingPlayerId);
				
				$playerRepository = new PlayerRepository();
				$role = $this->actualPlayer->getRoleObject();
				
				if ($role['type'] == Role::BANDIT) {
					if ($playerRepository->getCountLivePlayersWithRoles($this->game['id'],
							array(Role::ROLE_BANDIT_1, Role::ROLE_BANDIT_2, Role::ROLE_BANDIT_3,
								Role::ROLE_RENEGARD_1, Role::ROLE_RENEGARD_2)) == 0) {
						$this->endGame(array(Role::ROLE_SHERIFF, Role::ROLE_VICE_1, Role::ROLE_VICE_2));
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
				} elseif ($role['type'] == Role::SHERIFF) {
					if ($playerRepository->getCountLivePlayersWithRoles($this->game['id']) == 1) {
						if ($playerRepository->getCountLivePlayersWithRoles($this->game['id'], array(Role::ROLE_RENEGARD_1)) == 1) {
							$this->endGame(array(Role::ROLE_RENEGARD_1));
						} elseif ($playerRepository->getCountLivePlayersWithRoles($this->game['id'], array(Role::ROLE_RENEGARD_1)) == 1) {
							$this->endGame(array(Role::ROLE_RENEGARD_2));
						} else {
							$this->endGame(array(Role::ROLE_BANDIT_1, Role::ROLE_BANDIT_2, Role::ROLE_BANDIT_3));
						}
					}
					else {
						$this->endGame(array(Role::ROLE_BANDIT_1, Role::ROLE_BANDIT_2, Role::ROLE_BANDIT_3));
					}
				} elseif ($role['type'] == Role::RENEGARD) {
					if ($playerRepository->getCountLivePlayersWithRoles($this->game['id'],
							array(Role::ROLE_BANDIT_1, Role::ROLE_BANDIT_2, Role::ROLE_BANDIT_3,
								Role::ROLE_RENEGARD_1, Role::ROLE_RENEGARD_2)) == 0) {
						$this->endGame(array(Role::ROLE_SHERIFF, Role::ROLE_VICE_1, Role::ROLE_VICE_2));
					}
				} elseif ($role['type'] == Role::VICE) {
					// TODO skontrolovat ci serif zahodi karty - mozno sa mu v niektorom dalsom kroku znovu nahodia jeho povodne karty
					$attackingRole = $this->attackingPlayer->getRoleObject();
					if ($attackingRole['type'] == Role::SHERIFF) {
						GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getHandCards(), 'hand');
						GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getTableCards(), 'table');
						GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getWaitCards(), 'wait');
					}
				}
			} elseif ($this->useCharacter === TRUE && $newLifes > 0) {
				// ak bol pouzity charakter a nebol to este posledny zivot
				if ($this->actualPlayer->getCharacter()->getIsElGringo()) {
					// el gringo
					$attackingPlayerHandCards = $this->attackingPlayer->getHandCards();
					if ($attackingPlayerHandCards) {
						$movedCards = array($attackingPlayerHandCards[array_rand($attackingPlayerHandCards)]);
						$retVal = GameUtils::moveCards($this->game, $movedCards, $this->attackingPlayer, 'hand', $this->actualPlayer, 'hand');
						$this->actualPlayer = $retVal['playerTo'];
						$this->attackingPlayer = $retVal['playerFrom'];
					}
				} elseif ($this->actualPlayer->getCharacter()->getIsBartCassidy()) {
					// bart cassidy
					$drawnCards = GameUtils::drawCards($this->game, 1);
					$handCards = unserialize($this->actualPlayer['hand_cards']);
					foreach ($drawnCards as $drawnCard) {
						$handCards[] = $drawnCard;
					}
					$this->actualPlayer['hand_cards'] = serialize($handCards);
					$this->actualPlayer = $this->actualPlayer->save(TRUE);
				}
			}

			// TODO pocitat skore - pocet zabitych hracov
		}
		$this->changeInterturn();
	}

	private function endGame($roles) {
		// TODO nacitat podla roles hracov ktori vyhrali hru - aj ti ktori su mrtvy v tom case
		
		// vytvorit nejaku tabulku hall of fame kde budu vyhry a prehry
		
		// vyhry a prehry za nejaku konkretnu rolu  - typ roly - cize je jedno ci si bandita1 alebo bandita2
		$message = array(
			'text' => 'vyhrali roles: ' . print_R($roles, TRUE),
			'user' => User::SYSTEM,
		);

		$this->addMessage($message);

		$this->game['status'] = Game::GAME_STATUS_ENDED;
		$this->game->save();
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => 'zobral si si jeden zivot',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' si zobral jeden zivot',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::SAVE_LAST_LIFE) {
			$message = array(
				'text' => 'zachranil si si posledny zivot pivom',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' si zachranil posledny zivot pivom',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NOT_LAST_LIFE) {
			$message = array(
				'text' => 'toto nie je tvoj posledny zivot, nemozes sa branit pivom',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::NO_BEER_ON_HAND) {
			$message = array(
				'text' => 'nemas pivo',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} elseif ($this->check == self::ONLY_TWO_PLAYERS_IN_GAME) {
			$message = array(
				'text' => 'nemozes pouzit pivo, hrate uz len dvaja',
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