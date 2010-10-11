<?php

class Command {
	
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
			Chat::addMessage('Príkaz ' . $command . ' neexistuje', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected function initGame($game) {
		self::$room = intval($_GET['id']);
		self::$game = $game;
		self::$loggedUser = User::whoIsLogged();
		if (self::$loggedUser) {
			foreach ($game['players'] as $player) {
				if (self::$loggedUser['id'] == $player['user']['id']) {
					self::$player = $player;
				}
			}
		}
	}
	
	protected static function createGame() {
		if (GameUtils::create()) {
			Chat::addMessage('Hra bola vytvorená. Použi príkaz ".join" a pripoj sa k nej.', self::$room, User::SYSTEM);
		}
		else {
			Chat::addMessage('Hra nebola vytvorená, pretože v miestnosti sa už hrá iná hra. Použi príkaz ".join" a pripoj sa k nej.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
	}
	
	protected static function joinGame() {
		$result = GameUtils::addPlayer(self::$game, self::$loggedUser['id']);
		if ($result == 1) {
			Chat::addMessage(self::$loggedUser['username'] . ' sa pridal k hre', self::$room, User::SYSTEM);
			Chat::addMessage('Pridal si sa k hre.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
		elseif ($result == 2) {
			Chat::addMessage('Už si zapojený do tejto hry.', self::$room, User::SYSTEM, self::$loggedUser['id']);
		}
		elseif ($result == 3) {
			Chat::addMessage('Nemôžeš sa zapojiť do hry, pretože hra už začala.', self::$room, User::SYSTEM, self::$loggedUser['id']);
			
		}
		else {
			Chat::addMessage('Nemôžeš sa zapojiť do hry, pretože v tejto miestnosti sa nehrá žiadna hra. Použi príkaz ".create".', self::$room, User::SYSTEM, self::$loggedUser['id']);
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
			if (self::$player['phase'] == 2) {
				if (self::$player->getCanPass()) {
					$next = GameUtils::getNextPosition(self::$game, self::$player['position']);
					GameUtils::setTurn(self::$game, $next);
					
					$playerRepository = new PlayerRepository();
					$nextPlayer = $playerRepository->getPlayerByGameAndPosition(self::$game['id'], $next);
					
					$nextPlayer->setPhase(1);
					self::$player->setPhase(0);
					
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
					
					// TODO check if card type exists
					$methodName = 'getHas' . $cardName . 'OnHand';
					$card = self::$player->$methodName();
					if ($card) {
						if ($card->getIsPuttable()) {
							GameUtils::putOnTable(self::$game, self::$player, $card);
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
	
	protected static function distance($params = null) {
		$matrix = unserialize(self::$game['distance_matrix']);
		if ($params) {
			$from = self::$loggedUser['username'];
			$to = $params[0];
			$message = 'Vzdialenosť ' . $from . ' => ' . $to . ' je ' . $matrix[$from][$to];
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
}

?>