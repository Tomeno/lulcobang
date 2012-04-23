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
		'create' => array('class' => 'CreateGameCommand'),
		'join' => array('class' => 'JoinGameCommand'),
		'init' => array('class' => 'InitGameCommand'),
		'choose_character' => array('class' => 'ChooseCharacterCommand'),
		'start' => array('class' => 'StartGameCommand'),
		'draw' => array('class' => 'DrawCommand'),
		'choose_cards' => array('class' => 'ChooseCardsCommand'),
		'throw' => array('class' => 'ThrowCommand'),
		'put' => array('class' => 'PutCommand'),
		'pass' => array('class' => 'PassCommand'),
		'bang' => array('class' => 'BangCommand'),
		'diligenza' => array('class' => 'DiligenzaCommand'),
		'wells_fargo' => array('class' => 'WellsFargoCommand'),
		'pony_express' => array('class' => 'PonyExpressCommand'),
	//	'beer' => array('class' => 'BeerCommand'),
	//	'life' => array('class' => 'LifeCommand'),
	
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
		$commandAlias = str_replace('.', '', $commandArray[0]);
		
		$commandAliasRepository = new CommandAliasRepository();
		$command = $commandAliasRepository->getOneByLocalizedCommandName($commandAlias);
		
		// toto asi vyhodime lebo vsetky commandy budu musiet byt v db ale zatial to tu necham lebo sa mi to nechce plnit aj pre en
		if ($command) {
			$commandName = $command['default_command_name'];
		} else {
			$commandName = $commandAlias;
		}

		$localizedParams = array_slice($commandArray, 1);
		if (array_key_exists($commandName, self::$commands)) {
			$commandClassName = self::$commands[$commandName]['class'];
			
			$params = array();
			$cardAliasRepository = new CardAliasRepository();
			foreach($localizedParams as $param) {
				$cardAlias = $cardAliasRepository->getOneByLocalizedCardName($param);
				if ($cardAlias) {
					$params[] = $cardAlias['default_card_name'];
				} else {
					$params[] = $param;
				}
			}

			$class = new $commandClassName($params, $game);
			return $class->execute();
		} else {
			throw new Exception('Command not found', 1332363146);// TODO add message command not found
		}
	}

	protected final function execute() {
		// TODO add method pre-check wich will check if game is created, if player is on turn etc - these things are common for all commands

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