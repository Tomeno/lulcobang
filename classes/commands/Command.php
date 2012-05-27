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
	 * localized command params
	 *
	 * @var	mixed
	 */
	protected $localizedParams = NULL;

	/**
	 * check result
	 *
	 * @var	mixed
	 */
	protected $check = NULL;

	/**
	 * cards
	 *
	 * @var	array<Card>
	 */
	protected $cards = array();

	/**
	 * command messages
	 * @var	array
	 */
	protected $messages = array();

	/**
	 * precheckers
	 * 
	 * @var	array
	 */
	protected $precheckers = array();

	/**
	 * precheck params
	 *
	 * @var	array
	 */
	protected $precheckersParams = array();

	/**
	 * map command to method and checkers
	 *
	 * @var array
	 */
	protected static $commands = array(
		'create' => array(
			'class' => 'CreateGameCommand',
			'precheckers' => array('GameChecker'),
			'precheckParams' => array('GameChecker' => 'noGameExists'),
		),
		'join' => array('class' => 'JoinGameCommand'),
		'init' => array('class' => 'InitGameCommand'),
		'choose_character' => array('class' => 'ChooseCharacterCommand'),
		'start' => array('class' => 'StartGameCommand'),
		'draw' => array('class' => 'DrawCommand'),
		'choose_cards' => array('class' => 'ChooseCardsCommand'),
		'throw' => array(
			'class' => 'ThrowCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHas###PLACEHOLDER###OnHand'
			),
		),
		'put' => array(
			'class' => 'PutCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker', 'CardChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => array('getHas###PLACEHOLDER###OnHand', '!getHas###PLACEHOLDER###OnTheTable', '!getHas###PLACEHOLDER###OnWait'),
				'CardChecker' => 'isPuttable',
			),
		),
		'pass' => array(
			'class' => 'PassCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
			),
		),
		'bang' => array('class' => 'BangCommand'),
		'diligenza' => array('class' => 'DiligenzaCommand'),
		'wells_fargo' => array('class' => 'WellsFargoCommand'),
		'pony_express' => array('class' => 'PonyExpressCommand'),
	//	'beer' => array('class' => 'BeerCommand'),
	//	'life' => array('class' => 'LifeCommand'),
	
	);

	private function  __construct($params, $localizedParams, $game) {
		$this->params = $params;
		$this->game = $game;
		$this->localizedParams = $localizedParams;

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
			foreach($localizedParams as $key => $param) {
				$cardAlias = $cardAliasRepository->getOneByLocalizedCardName($param);
				if ($cardAlias) {
					$params[$key] = $cardAlias['default_card_name'];
				} else {
					$params[$key] = $param;
				}
			}

			$class = new $commandClassName($params, $localizedParams, $game);
			$precheckers = array();
			if (self::$commands[$commandName]['precheckers']) {
				$precheckers = self::$commands[$commandName]['precheckers'];
			}
			$class->setPrecheckers($precheckers);

			$precheckParams = array();
			if (self::$commands[$commandName]['precheckParams']) {
				$precheckParams = self::$commands[$commandName]['precheckParams'];
			}
			$class->setPrecheckersParams($precheckParams);

			return $class->execute();
		} else {
			throw new Exception('Command not found', 1332363146);// TODO add message command not found
		}
	}

	protected final function execute() {
		if ($this->precheck()) {
			$this->check();
			$this->run();
			$this->generateMessages();
		}
		$this->write();
		return $this->createResponse();
	}

	protected function precheck() {
		$check = TRUE;
		foreach ($this->getPrecheckers() as $prechecker) {
			$precheckerParams = $this->getPrecheckerParams($prechecker);
			$precheckClass = new $prechecker($this, $precheckerParams);
			$check = $precheckClass->check();

			if ($check === FALSE) {
				break;
			}
		}
		return $check;
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

	public function getRoom() {
		return $this->room;
	}

	public function setRoom($room) {
		$this->room = $room;
	}

	public function getGame() {
		return $this->game;
	}

	public function setGame($game) {
		$this->game = $game;
	}

	public function getLoggedUser() {
		return $this->loggedUser;
	}

	public function setLoggedUser($loggedUser) {
		$this->loggedUser = $loggedUser;
	}

	public function getActualPlayer() {
		return $this->actualPlayer;
	}

	public function setActualPlayer($actualPlayer) {
		$this->actualPlayer = $actualPlayer;
	}

	public function getPlayers() {
		return $this->players;
	}

	public function setPlayers($players) {
		$this->players = $players;
	}

	public function getParams() {
		return $this->params;
	}

	public function setParams($params) {
		$this->params = $params;
	}

	public function getLocalizedParams() {
		return $this->localizedParams;
	}

	public function getCheck() {
		return $this->check;
	}

	public function setCheck($check) {
		$this->check = $check;
	}

	public function getMessages() {
		return $this->messages;
	}

	public function setMessages($messages) {
		$this->messages = $messages;
	}

	public function addMessage($message) {
		// vsetky message su pridavane v akutalnej miestnosti ale keby nahodou bolo nastavene nieco ine tak to neprepiseme
		if (!$message['room']) {
			$room = $this->getRoom();
			$message['room'] = $room['id'];
		}
		$this->messages[] = $message;
	}

	protected function getPrecheckers() {
		return $this->precheckers;
	}

	protected function setPrecheckers($precheckers) {
		$this->precheckers = $precheckers;
	}
	
	protected function getPrecheckersParams() {
		return $this->precheckersParams;
	}

	protected function getPrecheckerParams($prechecker) {
		if ($this->precheckersParams[$prechecker]) {
			return $this->precheckersParams[$prechecker];
		} else {
			return array();
		}
	}

	protected function setPrecheckersParams(array $precheckersParams) {
		foreach ($precheckersParams as $prechecker => $params) {
			$this->addPrecheckerParams($prechecker, $params);
		}
	}

	protected function addPrecheckerParams($prechecker, $params) {
		if (!is_array($params)) {
			$params = array($params);
		}
		$this->precheckersParams[$prechecker] = $params;
	}

	public function addCard(Card $card) {
		$this->cards[] = $card;
	}

	public function getCards() {
		return $this->cards;
	}
}

?>