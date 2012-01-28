<?php

class OldCommand {
	
	/**
	 * map command to method
	 *
	 * @var array
	 */
	protected static $commands = array(
		'.create' => array('action' => 'createGame'),
		'.join' => array('action' => 'joinGame'),
		'.start' => array('action' => 'startGame'),
		'.tahaj' => array('action' => 'tahaj'),
		'.dostavnik' => array('action' => 'dostavnik'),
		'.wells_fargo' => array('action' => 'wellsFargo'),
		'.vyloz' => array('action' => 'putCard', 'arguments' => true),
		'.vyhod' => array('action' => 'throwCard', 'arguments' => true),
		'.pass' => array('action' => 'pass'),
		'.vzdialenost' => array('action' => 'distance', 'arguments' => true),
		'.bang' => array('action' => 'bang', 'arguments' => true),
		'.zivot' => array('action' => 'life'),
		'.pivo' => array('action' => 'beer'),
	);
	
	/**
	 * game
	 *
	 * @var Game
	 */
	protected static $game = null;
	
	/**
	 * room
	 *
	 * @var int
	 */
	protected static $room = null;
	
	/**
	 * logged user
	 *
	 * @var User
	 */
	protected static $loggedUser = null;
	
	/**
	 * player
	 *
	 * @var Player
	 */
	protected static $player = null;
	
	public static function execute($command, $game) {
		self::initGame($game);
		$commandArray = explode(' ', $command);
		$command = $commandArray[0];
		$params = array_slice($commandArray, 1);
		if (array_key_exists($command, self::$commands)) {
			$method = self::$commands[$command]['action'];
			if (self::$commands[$command]['arguments']) {
				self::$method($params);
			}
			else {
				self::$method();
			}
		}
		else {
			Chat::addMessage('Príkaz "' . $command . '" neexistuje.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected function initGame($game) {
		$roomAlias = Utils::get('identifier');
		$roomRepository = new RoomRepository();
		$room = $roomRepository->getOneByAlias($roomAlias);
		self::$room = $room['id'];

		self::$loggedUser = LoggedUser::whoIsLogged();
		if ($game && self::$loggedUser) {
			self::$game = $game;
			foreach ($game['players'] as $player) {
				if (self::$loggedUser['id'] == $player['user']['id']) {
					self::$player = $player;
					break;
				}
			}
		}
	}
	
	protected static function startGame() {
		$result = GameUtils::start(self::$game);
		if ($result == 1) {
			Chat::addMessage('Hra bola spustená. Na ťahu je šerif.', self::$room, User::SYSTEM);
		}
		elseif ($result == 2) {
			Chat::addMessage('Hra už je spustená.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
		elseif ($result == 3) {
			Chat::addMessage('Do hry musia byť zapojení aspoň 2 hráči.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
		else {
			Chat::addMessage('V miestnosti nie je vytvorená žiadna hra. Použi príkaz ".create".', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected static function tahaj() {
		if (GameUtils::checkTurn(self::$game, self::$player)) {
			if (self::$player['phase'] == 1) {
				
				// TODO podla charakterov zo specialnymi vlastnostami tu treba cosi spravit
				
				$cards = 2;
				GameUtils::getCards(self::$game, self::$player, $cards);
				GameUtils::setPhase(self::$game, self::$player, 2);
				Chat::addMessage(self::$loggedUser['username'] . ' si potiahol ' . $cards . ' karty.', self::$room, User::SYSTEM);
			}
			else {
				Chat::addMessage('Už si ťahal, teraz rob niečo iné alebo použi príkaz ".pass" a prenechaj ťah ďalšiemu hráčovi.', self::$room, User::SYSTEM, self::$loggedUser['id']);
			}
		}
		else {
			Chat::addMessage('Nemôžeš ťahať, pretože nie si na rade.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected static function pass() {
		if (GameUtils::checkTurn(self::$game, self::$player)) {
			if (self::$game['inter_turn_reason'] == '') {
				if (self::$player['phase'] == 2) {
					if (self::$player->getCanPass()) {
						$next = GameUtils::getNextPosition(self::$game, self::$player['position']);
						GameUtils::setTurn(self::$game, $next);
						GameUtils::setInterTurn(self::$game, 0);
						
						$playerRepository = new PlayerRepository();
						$nextPlayer = $playerRepository->getPlayerByGameAndPosition(self::$game['id'], $next);
						
						$nextPlayer->setPhase(1);
						self::$player->setPhase(0);
						self::$player->setUseBang(0);
						
						Chat::addMessage('Posunul si ťah.', self::$room, User::SYSTEM, self::$loggedUser['id']);
						Chat::addMessage('Si na ťahu, použi príkaz ".tahaj"', self::$room, User::SYSTEM, $nextPlayer['user']['id']);
					}
					else {
						Chat::addMessage('Nemôžeš posunúť ťah, pretože máš na ruke priveľa kariet', self::$room, User::SYSTEM, self::$loggedUser['id']);
					}
				}
				else {
					Chat::addMessage('Nemôžeš posunúť ťah, pretože si ešte neťahal karty.', self::$room, User::SYSTEM, self::$loggedUser['id']);
				}
			}
			else {
				Chat::addMessage('musis reagovat na ' . self::$game['inter_turn_reason'], self::$room, User::SYSTEM, self::$loggedUser['id']);
			}
		}
		else {
			Chat::addMessage('Nemôžeš posunúť ťah, pretože nie si na rade.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected static function dostavnik() {
		if (GameUtils::checkTurn(self::$game, self::$player)) {
			if (self::$player['phase'] == 2) {
				$dostavnik = self::$player->getHasDostavnikOnHand();
				if ($dostavnik) {
					GameUtils::throwCard(self::$game, self::$player, $dostavnik);
					GameUtils::getCards(self::$game, self::$player, 2);
				}
				else {
					Chat::addMessage('Nemáš DOSTAVNÍK.', self::$room, User::SYSTEM, self::$loggedUser['id']);
				}
			}
			else {
				Chat::addMessage('Najprv musíš potiahnuť karty. Použi príkaz ".tahaj".', self::$room, User::SYSTEM, self::$loggedUser['id']);
			}
		}
		else {
			Chat::addMessage('Nie si na rade.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected static function wellsFargo() {
		if (GameUtils::checkTurn(self::$game, self::$player)) {
			if (self::$player['phase'] == 2) {
				$wellsFargo = self::$player->getHasWellsFargoOnHand();
				if ($wellsFargo) {
					GameUtils::throwCard(self::$game, self::$player, $wellsFargo);
					GameUtils::getCards(self::$game, self::$player, 3);
				}
				else {
					Chat::addMessage('Nemáš WELLS FARGO.', self::$room, User::SYSTEM, self::$loggedUser['id']);
				}
			}
			else {
				Chat::addMessage('Najprv musíš potiahnuť karty. Použi príkaz ".tahaj".', self::$room, User::SYSTEM, self::$loggedUser['id']);
			}
		}
		else {
			Chat::addMessage('Nie si na rade.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected static function putCard($params) {
		if (GameUtils::checkTurn(self::$game, self::$player)) {
			if (self::$player['phase'] == 2) {
				if ($params) {
					$cardName = ucfirst(strtolower($params[0]));
					
					$methodName = 'getHas' . $cardName . 'OnHand';
					$card = self::$player->$methodName();
					if ($card) {
						if ($card->getIsPuttable()) {
							$playerTo = null;
							if ($card->getIsVazenie()) {
								if (isset($params[1])) {
									$rival = $params[1];
									if (self::$loggedUser['username'] == $rival) {
										Chat::addMessage('Nemôžeš dať do väzenie samého seba.', self::$room, User::SYSTEM, self::$loggedUser['id']);
									}
									else {
										$rivalPlayer = self::$game->getPlayerByUsername($rival);
										if ($rivalPlayer['role']['type'] == Role::SHERIFF) {
											Chat::addMessage('Nemôžeš dať do väzenie šerifa.', self::$room, User::SYSTEM, self::$loggedUser['id']);
										}
										else {
											$playerTo = $rivalPlayer;
										}
									}
								}
								else {
									Chat::addMessage('Musíš určiť, ktorého hráča chceš dať do väzenia.', self::$room, User::SYSTEM, self::$loggedUser['id']);
								}
							}
							elseif ($card->getIsGun() && self::$player->getHasGun()) {
								// TODO musi najprv odhodit staru zbran ak si chce vylozit novu
							}
							
							GameUtils::putOnTable(self::$game, self::$player, $card, $playerTo);
						}
						else {
							Chat::addMessage('Nemôžeš vyložiť kartu ' . strtolower($cardName), self::$room, User::SYSTEM, self::$loggedUser['id']);
						}
					}
					elseif ($card === 0) {
						Chat::addMessage('Karta ' . strtolower($cardName) . ' neexistuje.', self::$room, User::SYSTEM, self::$loggedUser['id']);
					}
					else {
						Chat::addMessage('Nemáš ' . strtolower($cardName), self::$room, User::SYSTEM, self::$loggedUser['id']);
					}
				}
				else {
					Chat::addMessage('Musíš určiť, ktorú kartu chceš vyložiť.', self::$room, User::SYSTEM, self::$loggedUser['id']);
				}
			}
			else {
				Chat::addMessage('Najprv musíš potiahnuť karty. Použi príkaz ".tahaj".', self::$room, User::SYSTEM, self::$loggedUser['id']);
			}
		}
		else {
			Chat::addMessage('Nie si na rade.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected static function throwCard($params) {
		if (GameUtils::checkTurn(self::$game, self::$player)) {
			if (self::$player['phase'] == 2) {
				if ($params) {
					$cardName = ucfirst(strtolower($params[0]));
					
					$place = 'Hand';
					if (strtolower($params[1]) == 'stol') {
						$place = 'TheTable';
					}
					$methodName = 'getHas' . $cardName . 'On' . $place;
					
					$card = self::$player->$methodName();
					if ($card) {
						if (!$card->getIsVezenie() && !$card->getIsDynamit()) {
							GameUtils::throwCard(self::$game, self::$player, $card, $place == 'Hand' ? 'hand' : 'table');
						}
					}
					elseif ($card === 0) {
						Chat::addMessage('Karta ' . strtolower($cardName) . ' neexistuje.', self::$room, User::SYSTEM, self::$loggedUser['id']);
					}
					else {
						Chat::addMessage('Nemáš ' . strtolower($cardName), self::$room, User::SYSTEM, self::$loggedUser['id']);
					}
				}
				else {
					Chat::addMessage('Musíš určiť, ktorú kartu chceš vyhodiť.', self::$room, User::SYSTEM, self::$loggedUser['id']);
				}
			}
			else {
				Chat::addMessage('Najprv musíš potiahnuť karty. Použi príkaz ".tahaj".', self::$room, User::SYSTEM, self::$loggedUser['id']);
			}
		}
		else {
			Chat::addMessage('Nie si na rade.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected static function distance($params = array()) {
		$matrix = unserialize(self::$game['distance_matrix']);
		if ($params) {
			$from = self::$loggedUser['username'];
			$to = $params[0];
			if (isset($matrix[$from][$to])) {
				$message = 'Vzdialenosť ' . $from . ' => ' . $to . ' je ' . $matrix[$from][$to];
			}
			else {
				$message = 'Hráč "' . $to . '" nehrá túto hru';
			}
		}
		else {
			$message = '<table>';
			$message .= '<tr><td>&nbsp;</td>';
			foreach (array_keys($matrix) as $player) {
				$message .= '<td>' . $player . '</td>';
			}
			$message .= '</tr>';
			foreach ($matrix as $from => $distances) {
				$message .= '<tr><td>' . $from . '</td>';
				foreach ($distances as $distance) {
					$message .= '<td style="text-align:center;">' . $distance . '</td>';
				}
				$message .= '</tr>';
			}
			$message .= '</table>';
		}
		Chat::addMessage($message, self::$room, User::SYSTEM, self::$loggedUser['id']);
	}
	
	protected static function bang($params = array()) {
		if (GameUtils::checkTurn(self::$game, self::$player)) {
			if ($game['inter_turn_reason'] == 'indiani') {
				
			}
			elseif ($game['inter_turn_reason'] == 'duel') {
				
			}
			else {
				if (self::$player['phase'] == 2) {
					$bang = self::$player->getHasBangOnHand();
					if ($bang) {
						if ($params[0]) {
							$matrix = unserialize(self::$game['distance_matrix']);
							$rival = $params[0];
							if (self::$loggedUser['username'] == $rival) {
								Chat::addMessage('Nemôžeš strieľať sám do seba.', self::$room, User::SYSTEM, self::$loggedUser['id']);
							}
							else {
								if (isset($matrix[self::$loggedUser['username']][$rival])) {
									$distance = $matrix[self::$loggedUser['username']][$rival];
									if (self::$player->getDostrel() >= $distance) {
										$rivalPlayer = self::$game->getPlayerByUsername($rival);
										GameUtils::setInterTurn(self::$game, $rivalPlayer['position'], 'bang');
										GameUtils::throwCard(self::$game, self::$player, $bang);
										self::$player->setUseBang(1);
										Chat::addMessage('Vystrelil si na ' . $rival, self::$room, User::SYSTEM, self::$loggedUser['id']);
										Chat::addMessage(self::$loggedUser['username'] . ' na teba vystrelil. Použi príkaz ".vedla" alebo ".zivot".', self::$room, User::SYSTEM, $rivalPlayer['user']['id']);
									}
									else {
										Chat::addMessage('Hráč "' . $rival . '" je od teba ďaleko, nedostrelíš na neho. Použi príkaz ".vyloz" na vyloženie zbrane.', self::$room, User::SYSTEM, self::$loggedUser['id']);
									}
								}
								else {
									Chat::addMessage('Hráč "' . $rival . '" nehrá túto hru.', self::$room, User::SYSTEM, self::$loggedUser['id']);
								}
							}
						}
						else {
							Chat::addMessage('Musíš určiť na koho strieľaš.', self::$room, User::SYSTEM, self::$loggedUser['id']);
						}
					}
					else {
						Chat::addMessage('Nemáš BANG!', self::$room, User::SYSTEM, self::$loggedUser['id']);
					}
				}
				else {
					Chat::addMessage('Najprv musíš potiahnuť karty. Použi príkaz ".tahaj".', self::$room, User::SYSTEM, self::$loggedUser['id']);
				}
			}
		}
		else {
			Chat::addMessage('Nie si na rade.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected static function life() {
		if (GameUtils::checkTurn(self::$game, self::$player)) {
			if (self::$game['inter_turn_reason']) {
				$actualLifes = self::$player->takeLife();
				if ($actualLifes == 0) {
					if (self::$player->getHasPivoOnHand()) {
						self::beer();
					}
					else {
						// TODO hrac je mrtvy, ak ho zabil iny hrac, pridat mu karty
						// preratat maticu
					}
				}
				
				// posunut tah na dalsieho hraca ale asi nejak podla inter_turn_reason
				if (self::$game['inter_turn_reason'] == 'bang') {
					GameUtils::setInterTurn(self::$game, 0);
				}
				elseif (self::$game['inter_turn_reason'] == 'indiani') {
					
				}
				
				// TODO next inter_turn_reasons
				
			}
			else {
				// nebude si brat zivot len tak pre nic za nic
			}
			
			
		}
		else {
			Chat::addMessage('Nie si na rade.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected static function beer() {
		if (GameUtils::checkTurn(self::$game, self::$player)) {
			if (self::$player['phase'] == 2 || self::$player['actual_lifes'] == 0) {
				$beer = self::$player->getHasPivoOnHand();
				if ($beer) {
					if (self::$player->addLife()) {
						GameUtils::throwCard(self::$game, self::$player, $beer);
					}
					else {
						// ma max zivotov
					}
				}
				else {
					// nema pivo
				}
			}
			else {
				// najprv musi tahat karty
			}
		}
		else {
			Chat::addMessage('Nie si na rade.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
}

?>