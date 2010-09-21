<?php

class Command {
	
	protected static $commands = array(
		'.create' => array('action' => 'createGame'),
		'.join' => array('action' => 'joinGame'),
		'.start' => array('action' => 'startGame'),
	);
	
	public static function execute($command) {
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
	
	protected static function createGame() {
		return Game::create();
	}
	
	protected static function joinGame() {
		$loggedUser = User::whoIsLogged();
		return $loggedUser['username'] . Game::addPlayer($loggedUser['id']);
	}
	
	protected static function startGame() {
		return Game::start();
	}
}

?>