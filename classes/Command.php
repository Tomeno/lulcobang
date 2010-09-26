<?php

class Command {
	
	protected static $commands = array(
		'.create' => array('action' => 'createGame'),
		'.join' => array('action' => 'joinGame'),
		'.start' => array('action' => 'startGame'),
		'.tahaj' => array('action' => 'tahaj'),
		'.dostavnik' => array('action' => 'dostavnik'),
		'.vyloz' => array('action' => 'putCard', 'arguments' => true),
		'.vyhod' => array('action' => 'throwCard', 'arguments' => true),
	);
	
	protected static $game = null;
	protected static $room = null;
	protected static $loggedUser = null;
	protected static $player = null;
	
	public static function execute($command, $game) {
		self::initGame($game);
		$commandArray = explode(' ', $command);
		$command = $commandArray[0];
		$params = array_slice($commandArray, 1);
		if (array_key_exists($command, self::$commands)) {
			$method = self::$commands[$command]['action'];
			if (self::$commands[$command]['arguments']) {
				return self::$method($params);
			}
			else {
				return self::$method();
			}
		}
		else {
			return 'prikaz ' . $command . ' neexistuje';
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
			Chat::addMessage('Hra bola vytvorená.', self::$room, User::SYSTEM);
		}
		else {
			Chat::addMessage('Hra nebola vytvorená, pretože v miestnosti sa už hrá iná hra.', self::$room, User::SYSTEM);
		}
	}
	
	protected static function joinGame() {
		$loggedUser = self::$loggedUser;
		$result = GameUtils::addPlayer(self::$game, $loggedUser['id']);
		if ($result == 1) {
			Chat::addMessage($loggedUser['username'] . ' sa pridal k hre', self::$room, User::SYSTEM);
			Chat::addMessage('Pridal si sa k hre', self::$room, User::SYSTEM, $loggedUser['id']);
		}
		elseif ($result == 2) {
			Chat::addMessage('Už si zapojený do tejto hry.', self::$room, User::SYSTEM, $loggedUser['id']);
		}
		elseif ($result == 3) {
			Chat::addMessage('Nemôžeš sa zapojiť do hry, pretože hra už začala.', self::$room, User::SYSTEM, $loggedUser['id']);
			
		}
		else {
			Chat::addMessage('Nemôžeš sa zapojiť do hry, pretože v tejto miestnosti sa nehrá žiadna hra.', self::$room, User::SYSTEM, $loggedUser['id']);
		}
	}
	
	protected static function startGame() {
		return GameUtils::start(self::$game);
	}
	
	protected static function tahaj() {
		// check turnt presunut sem
		if (self::$player['phase'] == 1) {
			
			// TODO podla charakterov zo specialnymi vlastnostami tu treba cosi spravit
			
			$ret = GameUtils::getCards(self::$game, self::$player, 2);
			GameUtils::setPhase(self::$game, self::$player, 2);
			return self::$loggedUser['username'] . $ret;
		}
		else {
			return 'uz si si tahal karty teraz rob nieco ine';
		}
	}
	
	protected static function dostavnik() {
		// TODO check turn
		if (self::$player['phase'] == 2) {
			$player = self::$player;
			$dostavnik = $player->getHasDostavnikOnHand();
			if ($dostavnik) {
				$ret = GameUtils::throwCards(self::$game, self::$player, $dostavnik);
				GameUtils::getCards(self::$game, self::$player, 2);
				return $ret;
			}
			return 'nemas dostavnik';
		}
		return ':)';
	}
	
	protected static function wellsFargo() {
		// TODO check turn
		if (self::$player['phase'] == 2) {
			$player = self::$player;
			$wellsFargo = $player->getHasWellsFargoOnHand();
			if ($wellsFargo) {
				$ret = GameUtils::throwCards(self::$game, self::$player, $wellsFargo);
				GameUtils::getCards(self::$game, self::$player, 3);
				return $ret;
			}
			return 'nemas wells fargo';
		}
		return ':)';
	}
	
	protected static function putCard($params) {
		$player = self::$player;
		// TODO check turn
		$cardName = ucfirst(strtolower($params[0]));
		
		// TODO check if card type exists
		$methodName = 'getHas' . $cardName . 'OnHand';
		$card = $player->$methodName();
		if ($card) {
			$ret = GameUtils::putOnTable(self::$game, self::$player, $card);
			//var_dump($ret);
		}
	}
	
	protected static function throwCard($params) {
		
	}
}

?>