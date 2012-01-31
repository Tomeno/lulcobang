<?php

abstract class Command {

	/**
	 * actual room
	 *
	 * @var	Room
	 */
	protected $room = NULL;

	/**
	 * actual game
	 *
	 * @var	Game
	 */
	protected $game = NULL;

	/**
	 * logged user
	 *
	 * @var	User
	 */
	protected $loggedUser = NULL;

	/**
	 * actual player
	 *
	 * @var	Player
	 */
	protected $actualPlayer = NULL;

	/**
	 * players in game
	 *
	 * @var	array<Player>
	 */
	protected $players = NULL;

	/**
	 * command params
	 *
	 * @var	mixed
	 */
	protected $params = NULL;

	/**
	 * check result
	 *
	 * @var	mixed
	 */
	protected $check = NULL;

	/**
	 * command messages
	 * @var	array
	 */
	protected $messages = array();

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
		'.start' => array('class' => 'StartGameCommand'),
		'.draw' => array('class' => 'DrawCommand'),
		'.choose_cards' => array('class' => 'ChooseCardsCommand'),
		'.throw' => array('class' => 'ThrowCommand'),
		'.pass' => array('class' => 'PassCommand'),
	//	'.diligenza' => array('class' => 'DiligenzaCommand'),
	//	'.wells_fargo' => array('class' => 'WellsFargoCommand'),
	//	'.beer' => array('class' => 'BeerCommand'),
	//	'.life' => array('class' => 'LifeCommand'),
	
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

	public final static function setup($command, $game) {
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

	protected final function execute() {
		$this->check();
		$this->run();
		$this->generateMessages();
		$this->write();
		return $this->createResponse();
	}

	abstract protected function check();

	abstract protected function run();

	abstract protected function generateMessages();

	protected final function write() {
		if ($this->messages && is_array($this->messages)) {
			foreach ($this->messages as $message) {
				Chat::addMessage($message);
			}
		}
	}

	abstract protected function createResponse();
}

?>