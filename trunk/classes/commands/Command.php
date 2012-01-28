<?php

abstract class Command {

	protected $room = NULL;

	/**
	 *
	 * @var	Game
	 */
	protected $game = NULL;

	protected $loggedUser = NULL;

	protected $actualPlayer = NULL;

	protected $players = NULL;

	protected $params = NULL;

	protected $check = NULL;


	/**
	 * map command to method
	 *
	 * @var array
	 */
	protected static $commands = array(
		'.create' => array('class' => 'CreateGameCommand'),
		'.join' => array('class' => 'JoinGameCommand'),
		'.init' => array('class' => 'InitGameCommand'),
		'.choose_character' => array('class' => 'ChooseCharacterCommand'),
	//	'.draw' => array('class' => 'DrawCommand'),
	//	'.diligenza' => array('class' => 'DiligenzaCommand'),
	//	'.wells_fargo' => array('class' => 'WellsFargoCommand'),
	//	'.beer' => array('class' => 'BeerCommand'),
	//	'.life' => array('class' => 'LifeCommand'),
	//	'.pass' => array('class' => 'PassCommand'),
	);

	private function  __construct($params, $game) {
		$this->params = $params;
		$this->game = $game;

		$roomAlias = Utils::get('identifier');
		$roomRepository = new RoomRepository();
		$room = $roomRepository->getOneByAlias($roomAlias);
		$this->room = $room;

		$this->loggedUser = LoggedUser::whoIsLogged();
		if ($this->game && $this->loggedUser) {
			$this->players = $this->game->getAdditionalField('players');
			foreach ($this->players as $player) {
				if ($this->loggedUser['id'] == $player['user']['id']) {
					$this->actualPlayer = $player;
					break;
				}
			}
		}
	}

	public static function setup($command, $game) {
		$commandArray = explode(' ', $command);
		$command = $commandArray[0];
		$params = array_slice($commandArray, 1);
		if (array_key_exists($command, self::$commands)) {
			$commandClassName = self::$commands[$command]['class'];
			$class = new $commandClassName($params, $game);
			return $class->execute();
		} else {
			// TODO add message command not found
		}
	}

	protected function execute() {
		$this->check();
		$this->run();
		$this->write();
		return $this->createResponse();
	}

	abstract protected function check();

	abstract protected function run();

	abstract protected function write();

	abstract protected function createResponse();
}

?>