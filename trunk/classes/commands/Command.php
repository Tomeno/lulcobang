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
	 * attacked player
	 *
	 * @var	Player
	 */
	protected $attackedPlayer = NULL;
	
	/**
	 * attacking player
	 *
	 * @var	Player
	 */
	protected $attackingPlayer = NULL;

	/**
	 * attacking cards
	 * 
	 * @var array<Card>
	 */
	protected $attackingCards = array();
	
	/**
	 * players in game
	 *
	 * @var	array<Player>
	 */
	protected $players = NULL;

	/**
	 * executed command name
	 * 
	 * @var	string
	 */
	protected $commandName = '';
	
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
	

	private function  __construct($params, $localizedParams, $game) {
		$this->params = $params;
		$this->game = $game;
		$this->localizedParams = $localizedParams;

		$this->interTurnReason = unserialize($this->game['inter_turn_reason']);
		$attackingPlayerId = $this->interTurnReason['from'];
		$playerRepository = new PlayerRepository();
		$this->attackingPlayer = $playerRepository->getOneById($attackingPlayerId);
		
		$cardRepository = new CardRepository();
		$this->attackingCards = $cardRepository->getById($this->interTurnReason['cards']);

		$roomRepository = new RoomRepository();
		if ($game) {
			$room = $roomRepository->getOneById($game['room']);
		} else {
			$roomAlias = Utils::get('identifier');
			$room = $roomRepository->getOneByAlias($roomAlias);
		}
		$this->room = $room;
		
		if ($this->game) {
			$this->players = $this->game->getAdditionalField('players');

			$attackedPlayer = $this->params['enemyPlayerUsername'];
			foreach ($this->players as $player) {
				$user = $player->getUser();
				if ($user['username'] == $attackedPlayer) {
					$this->attackedPlayer = $player;
					break;
				}
			}
		}
	}

	public final static function setup($command, $game, $actualPlayer = NULL) {
		$loggedUser = NULL;
		if ($actualPlayer === NULL) {
			$loggedUser = LoggedUser::whoIsLogged();
			if ($game && $loggedUser) {
				$players = $game->getAdditionalField('players');
				foreach ($players as $player) {
					if ($loggedUser['id'] == $player['user']['id']) {
						$actualPlayer = $player;
						break;
					}
				}
			}
		} else {
			$loggedUser = $actualPlayer->getUser();
		}
		
		$explodedCommand = explode('&', $command);
		$commandArray = array();
		foreach ($explodedCommand as $commandParam) {
			$explodedCommandParam = explode('=', $commandParam);
			$key = $explodedCommandParam[0];
			$value = isset($explodedCommandParam[1]) ? $explodedCommandParam[1] : '';
			if ($key == 'place' && $value == '') {
				$value = 'hand';
			}
			$commandArray[$key] = $value;
		}
	//	print_r($commandArray);

		$commandName = $commandArray['command'];
		$useCharacter = FALSE;
		// check if use character is set
		if ($commandArray[useCharacter] == 1) {
			$useCharacter = TRUE;
		}
		/*
		$commandAliasRepository = new CommandAliasRepository();
		$command = $commandAliasRepository->getOneByLocalizedCommandName($commandAlias);
		
		// toto asi vyhodime lebo vsetky commandy budu musiet byt v db ale zatial to tu necham lebo sa mi to nechce plnit aj pre en
		if ($command) {
			$commandName = $command['default_command_name'];
		} else {
			$commandName = $commandAlias;
		}*/

		if ($useCharacter === TRUE) {
//			$loggedUser = LoggedUser::whoIsLogged();
//			$playerRepository = new PlayerRepository();
//			$actualPlayer = $playerRepository->getOneByUserAndGame($loggedUser['id'], $game['id']);

			if ($actualPlayer->getIsCalamityJanet($game)) {
				// kvoli calamity janet musime vymenit bang a missed ak pouziva svoj charakter
				// uprava sa tyka aj metod v ActualPlayerHasCardsChecker getHasBang/MissedOnHand()
				if (in_array($commandName, array('bang', 'missed'))) {
					if ($commandName == 'bang') {
						$commandName = 'missed';
					} elseif ($commandName == 'missed') {
						$commandName = 'bang';
					}
				}
			} elseif ($actualPlayer->getIsElenaFuente($game)) {
				// ak je elena fuente pod utokmi a pouziva svoj charakter, berieme to ako keby pouzivala missed
				// uprava sa tyka aj metody v ActualPlayerHasCardsChecker
				if ($actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
					$commandName = 'missed';
				}
			} elseif ($actualPlayer->getIsAnnieVersary($game)) {
				// Annie Versary moze pouzit akukolvek kartu z ruky ako Bang!
				// uprava sa tyka aj metody v ActualPlayerHasCardsChecker
				$commandName = 'bang';
			} elseif ($actualPlayer->getIsUncleWill($game)) {
				// Uncle Will moze pouzit akukolvek kartu z ruky ako General store!
				// uprava sa tyka aj metody v ActualPlayerHasCardsChecker
				$commandName = 'generalstore';
			}
		}
		
		$commandClassName = CommandSetup::getCommandClass($commandName);
		if ($commandClassName !== '') {
			$class = new $commandClassName($commandArray, $commandArray, $game);
			$class->setCommandName($commandName);
			$class->setUseCharacter($useCharacter);
			$class->setActualPlayer($actualPlayer);
			$class->setLoggedUser($loggedUser);
			$precheckers = CommandSetup::getCommandPrecheckers($commandName);
			$class->setPrecheckers($precheckers);

			$precheckParams = CommandSetup::getCommandPrecheckParams($commandName);
			$class->setPrecheckersParams($precheckParams);

			return $class->execute();
		} else {
			throw new Exception('Command "' . $commandName . '" not found', 1332363146);// TODO add message command not found
		}
	}

	protected final function execute() {
		if ($this->precheck()) {
			$this->check();
			$this->run();
			$this->generateMessages();
		}
		$this->runSuzyLafayetteAction();
		$this->write();
		
		if ($this->game) {
			$this->game = $this->game->save(TRUE);

			$playerOnTurnId = $this->game['inter_turn'] ? $this->game['inter_turn'] : $this->game['turn'];

			$playerRepository = new PlayerRepository();
			$playerOnTurn = $playerRepository->getOneById(intval($playerOnTurnId));
			if ($playerOnTurn !== NULL && $playerOnTurn->getIsAi() && ($this->game['status'] == Game::GAME_STATUS_STARTED)) {
				$playerOnTurn->play($this->game);
			} else {
				return $this->createResponse();
			}
		} else {
			return $this->createResponse();
		}
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
		
		// vsetky message su pridavane k aktualnej hre
		if (!$message['game']) {
			$game = $this->getGame();
			$message['game'] = $game['id'];
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
	
	public function getCardIds() {
		$cardIds = array();
		foreach ($this->getCards() as $card) {
			$cardIds[] = $card['id'];
		}
		return $cardIds;
	}

	public function getEnemyPlayer() {
		if ($this->enemyPlayer) {
			return $this->enemyPlayer;
		} else {
			return $this->attackedPlayer;
		}
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
	
	public function setCommandName($commandName) {
		$this->commandName = $commandName;
	}
	
	public function getCommandName() {
		return $this->commandName;
	}
	
	protected function changeInterturn() {
		$attackingPlayerNotices = $this->attackingPlayer->getNoticeList();
		if ($this->attackingPlayer->getIsSlabTheKiller($this->game) && $attackingPlayerNotices['character_used'] &&
			in_array($this->interTurnReason['action'], array('bang')) && $this->commandName != 'life') {
			// zrusime slab the killerovi prvu ranu, dalsie vedla by uz malo ist do else vetvy
			if (isset($attackingPlayerNotices['character_used'])) {
				unset($attackingPlayerNotices['character_used']);
			}
			$this->attackingPlayer->setNoticeList($attackingPlayerNotices);
			$this->attackingPlayer->save();
		} else {
			if (in_array($this->interTurnReason['action'], array('indians', 'gatling', 'howitzer', 'wild_band'))) {
				$nextPositionPlayer = $this->getNextPositionPlayer($this->game, $this->actualPlayer);
				// ak je hrac na nasledujucej pozicii ten ktory utocil, ukoncime inter turn
				if ($nextPositionPlayer['id'] == $this->attackingPlayer['id']) {
					$this->game['inter_turn_reason'] = '';
					$this->game['inter_turn'] = 0;

					if ($this->attackingPlayer->getIsBelleStar($this->game)) {
						$attackingPlayerNotices = $this->attackingPlayer->getNoticeList();
						if (isset($attackingPlayerNotices['character_used'])) {
							unset($attackingPlayerNotices['character_used']);
						}
						$this->attackingPlayer->setNoticeList($attackingPlayerNotices);
					}

					$this->attackingPlayer['phase'] = Player::PHASE_PLAY;
					$this->attackingPlayer->save();
				} else {
					// inak nastavime pokracovanie interturnu
					$nextPositionPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
					$nextPositionPlayer->save();

					$this->game['inter_turn_reason'] = serialize(array(
						'action' => $this->interTurnReason['action'],
						'from' => $this->attackingPlayer['id'],
						'to' => $nextPositionPlayer['id'],
						'cards' => $this->interTurnReason['cards']
					));
					$this->game['inter_turn'] = $nextPositionPlayer['id'];
				}
			} elseif (in_array($this->interTurnReason['action'], array('fanning'))) {
				if (isset($this->interTurnReason['additionalTo'])) {
					$playerRepository = new PlayerRepository();
					$nextPlayer = $playerRepository->getOneById($this->interTurnReason['additionalTo']);
					$nextPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
					$nextPlayer->save();
					
					$this->game['inter_turn_reason'] = serialize(array(
						'action' => $this->interTurnReason['action'],
						'from' => $this->attackingPlayer['id'],
						'to' => $this->interTurnReason['additionalTo'],
						'cards' => $this->interTurnReason['cards']
					));
					$this->game['inter_turn'] = $this->interTurnReason['additionalTo'];
				} else {
					// ukoncime interturn
					$this->game['inter_turn_reason'] = '';
					$this->game['inter_turn'] = 0;

					if ($this->attackingPlayer->getIsBelleStar($this->game) || $this->attackingPlayer->getIsSlabTheKiller($this->game)) {
						$attackingPlayerNotices = $this->attackingPlayer->getNoticeList();
						if (isset($attackingPlayerNotices['character_used'])) {
							unset($attackingPlayerNotices['character_used']);
						}
						$this->attackingPlayer->setNoticeList($attackingPlayerNotices);
					}
					$this->attackingPlayer['phase'] = Player::PHASE_PLAY;
					$this->attackingPlayer->save();
				}
			} elseif (in_array($this->interTurnReason['action'], array('poker'))) {
				$nextPositionPlayer = $this->findNextPlayerWithHandCards($this->game, $this->actualPlayer, $this->attackingPlayer);
				// ak je hrac na nasledujucej pozicii ten ktory utocil, ukoncime inter turn
				if ($nextPositionPlayer['id'] == $this->attackingPlayer['id']) {
					
					$this->game['inter_turn_reason'] = '';
					$this->game['inter_turn'] = 0;
					
					$cardRepository = new CardRepository();
					$possibleCards = array();
					$containAce = FALSE;
					foreach ($this->interTurnReason['thrownCards']as $cardId) {
						$possibleCard = $cardRepository->getOneById($cardId);
						if ($possibleCard['value'] == 'A') {
							$containAce = TRUE;
						}
						$possibleCards[] = $possibleCard;
					}

					if ($containAce === TRUE) {
						// ak je vyhodene aspon jedno eso
						$this->attackingPlayer['phase'] = Player::PHASE_PLAY;
						
						// odhodime vsetky karty, ktore hraci vylozili
						$throwPile = unserialize($this->game['throw_pile']);
						foreach ($this->interTurnReason['thrownCards']as $cardId) {
							$throwPile[] = $cardId;
						}
						$this->game['throw_pile'] = serialize($throwPile);
					} else {
						// inak vybera 2 karty z vyhoenych
						MySmarty::assign('possiblePickCount', 2);
						MySmarty::assign('possibleCards', $possibleCards);
						MySmarty::assign('possibleCardsCount', count($possibleCards));
						MySmarty::assign('game', $this->game);
						$response = MySmarty::fetch('cards-choice.tpl');

						$playerPossibleChoices = array(
							'drawn_cards' => $this->interTurnReason['thrownCards'],
							'possible_pick_count' => 2,
							'rest_action' => 'throw',
						);

						$this->attackingPlayer['phase'] = Player::PHASE_POKER_SELECT;
						$this->attackingPlayer['possible_choices'] = serialize($playerPossibleChoices);
						$this->attackingPlayer['command_response'] = $response;
						
					}
					$this->attackingPlayer->save();
					
					
				} else {
					// inak nastavime pokracovanie interturnu
					$nextPositionPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
					$nextPositionPlayer->save();

					$this->game['inter_turn_reason'] = serialize(array(
						'action' => $this->interTurnReason['action'],
						'from' => $this->attackingPlayer['id'],
						'to' => $nextPositionPlayer['id'],
						'thrownCards' => $this->interTurnReason['thrownCards'],
					));
					$this->game['inter_turn'] = $nextPositionPlayer['id'];
				}
			} else {
				// ukoncime interturn
				$this->game['inter_turn_reason'] = '';
				$this->game['inter_turn'] = 0;

				if ($this->attackingPlayer->getIsBelleStar($this->game) || $this->attackingPlayer->getIsSlabTheKiller($this->game)) {
					$attackingPlayerNotices = $this->attackingPlayer->getNoticeList();
					if (isset($attackingPlayerNotices['character_used'])) {
						unset($attackingPlayerNotices['character_used']);
					}
					$this->attackingPlayer->setNoticeList($attackingPlayerNotices);
				}
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
			if (isset($notices['character_used'])) {
				unset($notices['character_used']);
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
		}
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
				// najprv pozrieme ci hrac nie je vera custer s charakterom zabiteho hraca, ak ano, vera uz nemoze mat jeho vlastnost
				if ($player->getIsVeraCuster($this->game)) {
					$notices = $player->getNoticeList();
					$actualPlayerCharacter = $this->actualPlayer->getCharacter();
					if (isset($notices['selected_character']) && $notices['selected_character'] == $actualPlayerCharacter['id']) {
						unset($notices['selected_character']);
					}
					$player->setNoticeList($notices);
					$player->save();
				}
				
				if ($player->getIsVultureSam($this->game)) {
					$vultureSams[] = $player;
				} elseif ($player->getIsGregDigger($this->game)) {
					$gregDiggers[] = $player;
				} elseif ($player->getIsHerbHunter($this->game)) {
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
	
		// znovunacitame game z databazy, lebo sa par veci zmenilo medzitym
		$gameRepository = new GameRepository();
		$this->game = $gameRepository->getOneById($this->game['id']);

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
		$playerRepository = new PlayerRepository();
		$players = $playerRepository->getByGameAndRole($this->game['id'], $roles);
		$playersNames = array();
		foreach ($players as $player) {
			$player['winner'] = 1;
			$player->save();
			$user = $player->getUser();
			$playersNames[] = $user['username'];
		}
		
		// znovu nacitame actual a attacking playera lebo to robi nejake halusky
		if ($this->actualPlayer) {
			$this->actualPlayer = $playerRepository->getOneById($this->actualPlayer['id']);
		}
		if ($this->attackingPlayer) {
			$this->attackingPlayer = $playerRepository->getOneById($this->attackingPlayer['id']);
		}
		
		// vytvorit nejaku tabulku hall of fame kde budu vyhry a prehry
		
		// vyhry a prehry za nejaku konkretnu rolu  - typ roly - cize je jedno ci si bandita1 alebo bandita2
		$message = array(
			'text' => 'vyhrali hraci: ' . implode(', ', $playersNames),
			'user' => User::SYSTEM,
		);

		$this->addMessage($message);

		$this->game['status'] = Game::GAME_STATUS_ENDED;
		$this->game->save();
	}
	
	protected function runMollyStarkAction() {
		if ($this->useCharacter === TRUE &&
			$this->actualPlayer->getIsMollyStark($this->game) &&
			$this->actualPlayer['phase'] == Player::PHASE_UNDER_ATTACK) {
		
			$drawnCards = GameUtils::drawCards($this->game, 1);
			$handCards = unserialize($this->actualPlayer['hand_cards']);
			$handCards = array_merge($handCards, $drawnCards);
			$this->actualPlayer['hand_cards'] = serialize($handCards);
		}
	}
	
	protected function runSuzyLafayetteAction() {
		if (!in_array($this->commandName, array('create', 'join', 'add_ai_player', 'init', 'choose_character', 'start'))) {
			if ($this->actualPlayer && $this->actualPlayer->getIsSuzyLafayette($this->game)) {
				if (!in_array($this->commandName, array('throw', 'draw', 'choose_cards'))) {
					$handCards = unserialize($this->actualPlayer['hand_cards']);
					if (count($handCards) == 0) {
						$drawnCards = GameUtils::drawCards($this->game, 1);
						$handCards = array_merge($handCards, $drawnCards);
						$this->actualPlayer['hand_cards'] = serialize($handCards);
						$this->actualPlayer = $this->actualPlayer->save(TRUE);
					}
				}
			} elseif ($this->enemyPlayer && $this->enemyPlayer->getIsSuzyLafayette($this->game)) {
				$handCards = unserialize($this->enemyPlayer['hand_cards']);
				if (count($handCards) == 0) {
					$drawnCards = GameUtils::drawCards($this->game, 1);
					$handCards = array_merge($handCards, $drawnCards);
					$this->enemyPlayer['hand_cards'] = serialize($handCards);
					$this->enemyPlayer = $this->enemyPlayer->save(TRUE);
				}
			}
		}
	}
	
	protected function getNextPositionPlayer($game, $actualPlayer) {
		$nextPositionPlayer = GameUtils::getPlayerOnNextPosition($game, $actualPlayer);
		if ($nextPositionPlayer['id'] == $this->attackingPlayer['id']) {
			return $nextPositionPlayer;
		} else {
			if ($nextPositionPlayer->getIsApacheKid($this->game)) {
				$isDiamonds = FALSE;
				foreach ($this->attackingCards as $attackingCard) {
					if ($attackingCard->getIsDiamonds($this->game)) {
						$isDiamonds = TRUE;
						break;
					}
				}
				if ($isDiamonds) {
					$nextPositionPlayer = $this->getNextPositionPlayer($game, $nextPositionPlayer);
				}
			}
			return $nextPositionPlayer;
		}
	}
	
	protected function findNextPlayerWithHandCards($game, $actualPlayer, $attackingPlayer) {
		$nextPositionPlayer = $this->getNextPositionPlayer($game, $actualPlayer);
		if ($nextPositionPlayer->getHandCards()) {
			return $nextPositionPlayer;
		} else {
			if ($nextPositionPlayer['id'] == $attackingPlayer['id']) {
				return $nextPositionPlayer;
			} else {
				return $this->findNextPlayerWithHandCards($game, $nextPositionPlayer, $attackingPlayer);
			}
		}
	}
	
	protected function checkCanAttackApacheKid() {
		$canAttack = TRUE;
		if ($this->attackedPlayer->getIsApacheKid($this->game)) {
			$isDiamonds = FALSE;
			foreach ($this->cards as $attackingCard) {
				if ($attackingCard->getIsDiamonds($this->game)) {
					$isDiamonds = TRUE;
					break;
				}
			}

			if ($isDiamonds) {
				$canAttack = FALSE;
			}
		}
		return $canAttack;
	}
	
	protected function getNextPhase(Player $player, $rattlesnakeDrawn = FALSE) {
		$phase = NULL;
		// TODO fistful of cards
		// TODO toto asi skipneme pri vendette, lebo sherif by hned tahal dalsiu kartu z rozsirenia - sice je tam phase none tak asi netreba
		if ($player->getRoleObject()->getIsSheriff() && $player['phase'] == Player::PHASE_NONE) {
			$phase = $this->checkSheriffExtensions();
		} elseif ($player['phase'] == Player::PHASE_NONE) {
			$phase = $this->checkOtherExtensions();
		}
		
		if ($phase === NULL) {
			// if has dynamite and/or jail - phase dynamite / jail, else phase draw
			if ($player->getHasDynamiteOnTheTable($this->game)) {
				$phase = Player::PHASE_DYNAMITE;
			} elseif ($player->getHasJailOnTheTable($this->game)) {
				$phase = Player::PHASE_JAIL;
			} elseif ($player->getHasRattlesnakeOnTheTable($this->game) && $rattlesnakeDrawn === FALSE) {
				$phase = Player::PHASE_RATTLESNAKE;
			} elseif ($player->getIsGaryLooter($this->game)) {
				$phase = Player::PHASE_PLAY;
			} else {
				$phase = Player::PHASE_DRAW;
			}
		}
		return $phase;
	}
	
	protected function checkSheriffExtensions() {
		if ($this->game->getIsHighNoon()) {
			if ($this->game->getHighNoonPile()) {
				return Player::PHASE_DRAW_HIGH_NOON;
			} else {
				return Player::PHASE_HIGH_NOON;
			}
		}
		return NULL;
	}
	
	protected function checkOtherExtensions() {
		if ($this->game->getIsHighNoon() && !$this->game->getHighNoonPile()) {
			return Player::PHASE_HIGH_NOON;
		}
		return NULL;
	}
}

?>