<?php

class Command {
	
	protected static $commands = array(
		'.create' => array('action' => 'createGame'),
		'.join' => array('action' => 'joinGame'),
		'.start' => array('action' => 'startGame'),
	);
	
	protected static $game = null;
	
	public static function execute($command, $game) {
		self::setGame($game);
		$commandArray = explode(' ', $command);
		$command = $commandArray[0];
		$params = array_slice($commandArray, 1);
		if (array_key_exists($command, self::$commands)) {
			$method = self::$commands[$command]['action'];
			return self::$method();
		}
		else {
			return 'prikaz ' . $command . ' neexistuje';
		}
	}
	
	protected function setGame($game) {
		self::$game = $game;
	}
	
	protected static function createGame() {
		return GameUtils::create();
	}
	
	protected static function joinGame() {
		$loggedUser = User::whoIsLogged();
		return $loggedUser['username'] . GameUtils::addPlayer(self::$game, $loggedUser['id']);
	}
	
	protected static function startGame() {
		return GameUtils::start(self::$game);
	}
}

?>