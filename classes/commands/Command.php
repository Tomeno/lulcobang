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
	 * interturn reason
	 *
	 * @var	string
	 */
	protected $interTurnReason = NULL;

	/**
	 * attacking player
	 *
	 * @var	Player
	 */
	protected $attackingPlayer = NULL;

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
	 * enemy player
	 * 
	 * @var	Player
	 */
	protected $enemyPlayer = NULL;
	
	/**
	 * enemy players cards
	 *
	 * @var	array<Card>
	 */
	protected $enemyPlayersCards = array();

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
	 * says use character - special way of command
	 * @var	boolean
	 */
	protected $useCharacter = FALSE;
	
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
		'join' => array(
			'class' => 'JoinGameCommand',
			//'precheckers' => array(),
		),
		'init' => array(
			'class' => 'InitGameCommand'
		),
		'choose_character' => array(
			'class' => 'ChooseCharacterCommand'
		),
		'start' => array(
			'class' => 'StartGameCommand'
		),
		'draw' => array(
			'class' => 'DrawCommand',
			'precheckers' => array('GameChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
			),
		),
		'choose_cards' => array(
			'class' => 'ChooseCardsCommand'
		),
		'throw' => array(
			'class' => 'ThrowCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHas###CARD_PLACEHOLDER######PLACE_PLACEHOLDER###'
			),
		),
		'put' => array(
			'class' => 'PutCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker', 'CardChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => array('getHas###CARD_PLACEHOLDER###OnHand', '!getHas###CARD_PLACEHOLDER###OnTheTable', '!getHas###CARD_PLACEHOLDER###OnWait'),
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
		'bang' => array(
			'class' => 'BangCommand',
			'precheckers' => array('GameChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'ActualPlayerHasCardsChecker' => 'getHasBangOnHand',
			),
		),
		'missed' => array(
			'class' => 'MissedCommand',
			'precheckers' => array('GameChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'ActualPlayerHasCardsChecker' => 'getHasMissedOnHand',
			),
		),
		'generalstore' => array(
			'class' => 'GeneralStoreCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasGeneralstoreOnHand',
			),
		),
		'dodge' => array(
			'class' => 'DodgeCommand',
			'precheckers' => array('GameChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isUnderAttack',
				'ActualPlayerHasCardsChecker' => 'getHasDodgeOnHand',
			),
		),
		'sombrero' => array(
			'class' => 'SombreroCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isUnderAttack',
				'ActualPlayerHasCardsChecker' => 'getHasSombreroOnTheTable',
			),
		),
		'ironplate' => array(
			'class' => 'IronPlateCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isUnderAttack',
				'ActualPlayerHasCardsChecker' => 'getHasIronplateOnTheTable',
			),
		),
		'tengallonhat' => array(
			'class' => 'TengallonhatCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isUnderAttack',
				'ActualPlayerHasCardsChecker' => 'getHasTengallonhatOnTheTable',
			),
		),
		'diligenza' => array(
			'class' => 'DiligenzaCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasDiligenzaOnHand',
			),
		),
		'wellsfargo' => array(
			'class' => 'WellsFargoCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasWellsfargoOnHand',
			),
		),
		'ponyexpress' => array(
			'class' => 'PonyExpressCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasPonyexpressOnTheTable',
			),
		),
		'catbalou' => array(
			'class' => 'CatbalouCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasCatbalouOnHand',
			),
		),
		'panic' => array(
			'class' => 'PanicCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasPanicOnHand',
			),
		),
		'beer' => array(
			'class' => 'BeerCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasBeerOnHand',
			),
		),
		'saloon' => array(
			'class' => 'SaloonCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasSaloonOnHand',
			),
		),
		'life' => array(
			'class' => 'LifeCommand',
			'precheckers' => array('GameChecker'/*, 'PlayerPhaseChecker'*/),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
			//	'PlayerPhaseChecker' => 'isUnderAttack',
			),
		),
		'jail' => array(
			'class' => 'JailCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasJailOnHand',
			),
		),
		'indians' => array(
			'class' => 'IndiansCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasIndiansOnHand',
			),
		),
		'gatling' => array(
			'class' => 'GatlingCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasGatlingOnHand',
			),
		),
		'pepperbox' => array(
			'class' => 'PepperboxCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasPepperboxOnTheTable',
			),
		),
		'knife' => array(
			'class' => 'KnifeCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasKnifeOnTheTable',
			),
		),
		'derringer' => array(
			'class' => 'DerringerCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasDerringerOnTheTable',
			),
		),
		'canteen' => array(
			'class' => 'CanteenCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasCanteenOnTheTable',
			),
		),
		'cancan' => array(
			'class' => 'CancanCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasCancanOnTheTable',
			),
		),
		'conestoga' => array(
			'class' => 'ConestogaCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasConestogaOnTheTable',
			),
		),
		'bible' => array(
			'class' => 'BibleCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isUnderAttack',
				'ActualPlayerHasCardsChecker' => 'getHasBibleOnTheTable',
			),
		),
		'buffalorifle' => array(
			'class' => 'BuffalorifleCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasBuffalorifleOnTheTable',
			),
		),
		'punch' => array(
			'class' => 'PunchCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasPunchOnHand',
			),
		),
		'duel' => array(
			'class' => 'DuelCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasDuelOnHand',
			),
		),
		'howitzer' => array(
			'class' => 'HowitzerCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasHowitzerOnTheTable',
			),
		),
		'tequila' => array(
			'class' => 'TequilaCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasTequilaOnHand',
			),
		),
		'whisky' => array(
			'class' => 'WhiskyCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasWhiskyOnHand',
			),
		),
		'ragtime' => array(
			'class' => 'RagTimeCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasRagTimeOnHand',
			),
		),
		'springfield' => array(
			'class' => 'SpringfieldCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasSpringfieldOnHand',
			),
		),
	);

	private function  __construct($params, $localizedParams, $game) {
		$this->params = $params;	// TODO mozno by bolo fajn tieto parametre nejako rozdelit na kagetorie ako command, card, player, place aby sa s tym dalo lepsie robit, lebo teraz nikdy neviem kde co hladat - napr. ktoru kartu vyhadzujem atd
		$this->game = $game;
		$this->localizedParams = $localizedParams;

		$this->interTurnReason = unserialize($this->game['inter_turn_reason']);
		$attackingPlayerId = $this->interTurnReason['from'];
		$playerRepository = new PlayerRepository();
		$this->attackingPlayer = $playerRepository->getOneById($attackingPlayerId);

		$roomRepository = new RoomRepository();
		if ($game) {
			$room = $roomRepository->getOneById($game['room']);
		} else {
			$roomAlias = Utils::get('identifier');
			$room = $roomRepository->getOneByAlias($roomAlias);
		}
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
		
//		$matrix = GameUtils::countMatrix($game);
//		$game['distance_matrix'] = serialize($matrix);
//		$game = $game->save(TRUE);

		$command = str_replace('.', '', $command);
		$commandArray = explode(' ', $command);
		$useCharacter = FALSE;
		// check if first part of command says: use character
		if ($commandArray[0] == 'char') {
			$commandAlias = $commandArray[1];
			$useCharacter = TRUE;
			$commandArraySlice = 2;
		} else {
			$commandAlias = $commandArray[0];
			$commandArraySlice = 1;
		}
		
		$commandAliasRepository = new CommandAliasRepository();
		$command = $commandAliasRepository->getOneByLocalizedCommandName($commandAlias);
		
		// toto asi vyhodime lebo vsetky commandy budu musiet byt v db ale zatial to tu necham lebo sa mi to nechce plnit aj pre en
		if ($command) {
			$commandName = $command['default_command_name'];
		} else {
			$commandName = $commandAlias;
		}

		if ($useCharacter === TRUE) {
			$loggedUser = LoggedUser::whoIsLogged();
			$playerRepository = new PlayerRepository();
			$actualPlayer = $playerRepository->getOneByUserAndGame($loggedUser['id'], $game['id']);

			if ($actualPlayer->getCharacter()->getIsCalamityJanet()) {
				// kvoli calamity janet musime vymenit bang a missed ak pouziva svoj charakter
				// uprava sa tyka aj metod v ActualPlayerHasCardsChecker getHasBang/MissedOnHand()
				if (in_array($commandName, array('bang', 'missed'))) {
					if ($actualPlayer->getCharacter()->getIsCalamityJanet()) {
						if ($commandName == 'bang') {
							$commandName = 'missed';
						} elseif ($commandName == 'missed') {
							$commandName = 'bang';
						}
					}
				}
			} elseif ($actualPlayer->getCharacter()->getIsElenaFuente()) {
				// ak je elena fuente pod utokmi a pouziva svoj charakter, berieme to ako keby pouzivala missed
				// uprava sa tyka aj metody v ActualPlayerHasCardsChecker
				if ($actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
					$commandName = 'missed';
				}
			}
		}
		
		$localizedParams = array_slice($commandArray, $commandArraySlice);
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
			$class->setUseCharacter($useCharacter);
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
		// ak nie je urcene od koho je sprava tak je od systemu
		if (!$message['user']) {
			$message['user'] = User::SYSTEM;
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

	public function getEnemyPlayer() {
		return $this->enemyPlayer;
	}

	public function addEnemyPlayerCard(Player $player, Card $card) {
		$this->enemyPlayersCards[$player['id']][] = $card;
	}

	public function addEnemyPlayerCards(Player $player, array $cards) {
		foreach ($cards as $card) {
			$this->addEnemyPlayerCard($player, $card);
		}
	}

	public function getEnemyPlayerCards() {
		return $this->enemyPlayersCards;
	}
	
	public function setUseCharacter($useCharacter) {
		$this->useCharacter = $useCharacter;
	}
	
	public function getUseCharacter() {
		return $this->useCharacter;
	}
	
	protected function changeInterturn() {
		if (in_array($this->interTurnReason['action'], array('indians', 'gatling', 'howitzer'))) {
			$nextPositionPlayer = GameUtils::getPlayerOnNextPosition($this->game, $this->actualPlayer);
			// ak je hrac na nasledujucej pozicii ten ktory utocil, ukoncime inter turn
			if ($nextPositionPlayer['id'] == $this->attackingPlayer['id']) {
				$this->game['inter_turn_reason'] = '';
				$this->game['inter_turn'] = 0;

				$this->attackingPlayer['phase'] = Player::PHASE_PLAY;
				$this->attackingPlayer->save();
			} else {
				$nextPositionPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
				$nextPositionPlayer->save();

				// inak nastavime pokracovanie interturnu
				$this->game['inter_turn_reason'] = serialize(array('action' => $this->interTurnReason['action'], 'from' => $this->attackingPlayer['id'], 'to' => $nextPositionPlayer['id']));
				$this->game['inter_turn'] = $nextPositionPlayer['id'];
			}
		} else {
			// ukoncime interturn
			$this->game['inter_turn_reason'] = '';
			$this->game['inter_turn'] = 0;

			$this->attackingPlayer['phase'] = Player::PHASE_PLAY;
			$this->attackingPlayer->save();
		}
		// premazeme notices
		$notices = $this->actualPlayer->getNoticeList();
		if (isset($notices['barrel_used'])) {
			unset($notices['barrel_used']);
		}
		if (isset($notices['character_jourdonnais_used'])) {
			unset($notices['character_jourdonnais_used']);
		}
		$this->actualPlayer->setNoticeList($notices);
		// aktualnemu hracovi nastavime fazu na none a response na nic vzdy
		if ($this->actualPlayer['id'] == $this->interTurnReason['to']) {
			$this->actualPlayer['phase'] = Player::PHASE_NONE;
		} elseif ($this->actualPlayer['id'] == $this->interTurnReason['from']) {
			$this->actualPlayer['phase'] = Player::PHASE_PLAY;
		} else {
			throw new Exception('Moze byt aktualny hrac niekto iny?', 1353360969);
		}
		$this->actualPlayer['command_response'] = '';
		$this->actualPlayer->save();

		$this->game->save();
	}
	
	protected function removePlayerFromGame() {
		$this->actualPlayer['actual_lifes'] = 0;
		$this->actualPlayer['position'] = 0;
		$this->actualPlayer['phase'] = 0;
		$this->actualPlayer = $this->actualPlayer->save(TRUE);
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
			$retVal = GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getHandCards(), 'hand');
			$this->game = $retVal['game'];
			$this->actualPlayer = $retVal['player'];
			$retVal = GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getTableCards(), 'table');
			$this->game = $retVal['game'];
			$this->actualPlayer = $retVal['player'];
			$retVal = GameUtils::throwCards($this->game, $this->actualPlayer, $this->actualPlayer->getWaitCards(), 'wait');
			$this->game = $retVal['game'];
			$this->actualPlayer = $retVal['player'];
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
				if ($this->attackingPlayer) {
					$drawnCards = GameUtils::drawCards($this->game, 3);
					$handCards = unserialize($this->attackingPlayer['hand_cards']);
					foreach ($drawnCards as $card) {
						$handCards[] = $card;
					}

					$this->attackingPlayer['hand_cards'] = serialize($handCards);
					$this->attackingPlayer = $this->attackingPlayer->save(TRUE);
				}
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
			if ($this->attackingPlayer) {
				$attackingRole = $this->attackingPlayer->getRoleObject();
				if ($attackingRole['type'] == Role::SHERIFF) {
					$retVal = GameUtils::throwCards($this->game, $this->attackingPlayer, $this->attackingPlayer->getHandCards(), 'hand');
					$this->game = $retVal['game'];
					$this->attackingPlayer = $retVal['player'];
					$retVal = GameUtils::throwCards($this->game, $this->attackingPlayer, $this->attackingPlayer->getTableCards(), 'table');
					$this->game = $retVal['game'];
					$this->attackingPlayer = $retVal['player'];
					$retVal = GameUtils::throwCards($this->game, $this->attackingPlayer, $this->attackingPlayer->getWaitCards(), 'wait');
					$this->game = $retVal['game'];
					$this->attackingPlayer = $retVal['player'];
					
					// kedze je mozne ze rusime nejaku modru kartu ktora ovplyvnuje vzdialenost, preratame maticu
					// ak to bude velmi pomale, budeme to robit len ak je medzi zrusenymi kartami fakt takato karta
					$matrix = GameUtils::countMatrix($this->game);
					$this->game['distance_matrix'] = serialize($matrix);
					$this->game->save();
				}
			}
		}
	}
	
	protected function endGame($roles) {
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
	
	protected function runMollyStarkAction() {
		if ($this->useCharacter === TRUE &&
			$this->actualPlayer->getCharacter()->getIsMollyStark() &&
			$this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
		
			$drawnCards = GameUtils::drawCards($this->game, 1);
			$handCards = unserialize($this->actualPlayer['hand_cards']);
			$handCards = array_merge($handCards, $drawnCards);
			$this->actualPlayer['hand_cards'] = serialize($handCards);
		}
	}
}

?>