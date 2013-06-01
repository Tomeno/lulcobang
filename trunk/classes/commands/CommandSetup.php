<?php

class CommandSetup {
	
	/**
	 * map command to method and checkers
	 *
	 * @var	array
	 */
	protected static $commands = array(
		'say' => array(
			'class' => 'SayCommand',
		),
		'create' => array(
			'class' => 'CreateGameCommand',
			'precheckers' => array('GameChecker'),
			'precheckParams' => array('GameChecker' => 'noGameExists'),
		),
		'join' => array(
			'class' => 'JoinGameCommand',
			//'precheckers' => array(),
		),
		'add_ai_player' => array(
			'class' => 'AddAiPlayerCommand',
			'precheckers' => array('GameChecker'),
			'precheckParams' => array('GameChecker' => 'gameExists'),
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
			'precheckers' => array('GameChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
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
		'brawl' => array(
			'class' => 'BrawlCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasBrawlOnHand',
			),
		),
		'draw_high_noon' => array(
			'class' => 'DrawHighNoonCommand',
		),
		'use_one_round_card' => array(
			'class' => 'UseOneRoundCardCommand',
		),
		'rattlesnake' => array(
			'class' => 'RattlesnakeCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasRattlesnakeOnHand',
			),
		),
		'bounty' => array(
			'class' => 'BountyCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasBountyOnHand',
			),
		),
		'lastbeer' => array(
			'class' => 'LastBeerCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasLastBeerOnHand',
			),
		),
		'aiming' => array(
			'class' => 'AimingCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasAimingOnHand',
			),
		),
		'tomahawk' => array(
			'class' => 'TomahawkCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasTomahawkOnHand',
			),
		),
		'tornado' => array(
			'class' => 'TornadoCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasTornadoOnHand',
			),
		),
		'ghost' => array(
			'class' => 'GhostCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasGhostOnHand',
			),
		),
		'backfire' => array(
			'class' => 'BackfireCommand',
			'precheckers' => array('GameChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isUnderAttack',
				'ActualPlayerHasCardsChecker' => 'getHasBackfireOnHand',
			),
		),
		'fanning' => array(
			'class' => 'FanningCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasFanningOnHand',
			),
		),
		'poker' => array(
			'class' => 'PokerCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasPokerOnHand',
			),
		),
		'wildband' => array(
			'class' => 'WildBandCommand',
			'precheckers' => array('GameChecker', 'PlayerPhaseChecker', 'ActualPlayerHasCardsChecker'),
			'precheckParams' => array(
				'GameChecker' => 'gameStarted',
				'PlayerPhaseChecker' => 'isInPlayPhase',
				'ActualPlayerHasCardsChecker' => 'getHasWildBandOnHand',
			),
		),
	);
	
	public static function getCommandSetup($command) {
		if (isset(self::$commands[$command])) {
			return self::$commands[$command];
		}
		return NULL;
	}
	
	public static function getCommandClass($command) {
		$commandSetup = self::getCommandSetup($command);
		if ($commandSetup !== NULL) {
			return $commandSetup['class'];
		}
		return '';
	}
	
	public static function getCommandPrecheckers($command) {
		$commandSetup = self::getCommandSetup($command);
		if ($commandSetup !== NULL && isset($commandSetup['precheckers']) && is_array($commandSetup['precheckers'])) {
			return $commandSetup['precheckers'];
		}
		return array();
	}
	
	public static function getCommandPrecheckParams($command) {
		$commandSetup = self::getCommandSetup($command);
		if ($commandSetup !== NULL && isset($commandSetup['precheckParams']) && is_array($commandSetup['precheckParams'])) {
			return $commandSetup['precheckParams'];
		}
		return array();
	}
}

?>