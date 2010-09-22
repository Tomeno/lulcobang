<?php

class Game extends Item {
	
	const GAME_STATUS_STARTED = 1;
	
	const GAME_STATUS_ENDED = 2;
	
	public function __construct($game) {
		parent::__construct($game);
		
		$cardRepository = new CardRepository();
		
		$drawPile = unserialize($game['draw_pile']);
		$drawPileCards = array();
		if ($drawPile) {
			foreach ($drawPile as $cardId) {
				$drawPileCards[] = $cardRepository->getOneById($cardId);
			}
		}
		$this->offsetSet('draw_pile', $drawPileCards);
		
		$throwPile = unserialize($game['throw_pile']);
		$throwPileCards = array();
		if ($throwPile) {
			foreach ($throwPile as $cardId) {
				$throwPileCards[] = $cardRepository->getOneById($cardId);
			}
		}
		$this->offsetSet('throw_pile', $throwPileCards);
		
		$playerRepository = new PlayerRepository();
		$players = $playerRepository->getByGame($game['id']);
		$this->offsetSet('players', $players);
	}
	
	/**
	 * @deprecated ?
	 *
	 * @return unknown
	 */
	protected static function load() {
		$room = intval($_GET['id']);
		
		$query = 'SELECT * FROM ' . self::$table . ' WHERE room = ' . $room;
		return $GLOBALS['db']->fetchFirst($query);
	}
	
	public static function create() {
		$room = intval($_GET['id']);
		
		$query = 'SELECT count(*) AS pocet FROM ' . self::$table . ' WHERE room = ' . $room;	// todo : doplnit nejaky status hry
		$game = $GLOBALS['db']->fetchFirst($query);
		if ($game['pocet'] > 0) {
			return 'Hra nebola vytvorená';
		}
		else {
			$params = array(
				'room' => $room,
			);
			$GLOBALS['db']->insert(self::$table, $params);
			return 'Hra bola vytvorená';
		}
	}
	
	public static function addPlayer($user) {
		$game = self::load();
		
		if ($game) {
			$gameId =  intval($game['id']);
			$query = 'SELECT count(*) AS pocet FROM ' . self::$gamePlayerTable . ' WHERE game = ' . $gameId . ' AND user = ' . intval($user); // TODO nejaky status hry aby sa nedalo pridat ak uz sa hra resp ak je skoncena
			$userCount = $GLOBALS['db']->fetchFirst($query);
			if ($userCount['pocet'] > 0) {
				return ' už je zapojený do tejto hry.';
			}
			else {
				$params = array(
					'game' => $gameId,
					'user' => intval($user),
					'position' => intval(self::getPosition($gameId)),
				);
				$GLOBALS['db']->insert(self::$gamePlayerTable, $params);
				return ' sa pridal k hre';
			}
		}
		return ' sa nemôže zapojiť do hry, pretože v tejto miestnosti sa nehrá žiadna hra.';
	}
	
	private static function getPosition($game) {
		$query = 'SELECT MAX(position) AS max_position FROM ' . self::$gamePlayerTable . ' WHERE game = ' . intval($game);
		$position = $GLOBALS['db']->fetchFirst($query);
		
		return $position['max_position'] + 1;
	}
	
	public function start() {
		$game = self::load();
		$players = self::getPlayers($game['id']);
		
		$roles = Role::getRoles(count($players));
		shuffle($roles);
		
		$characters = Character::getCharacters();
		shuffle($characters);
		
		$cards = Card::getCardIds();
		shuffle($cards);
		
		$j = 0;
		foreach ($players as $player) {
			$playerCards = array();
			$params = array();
			
			$params['role'] = $roles[$j]['id'];
			$params['charakter'] = $characters[$j]['id'];
			$params['actual_lifes'] = $characters[$j]['lifes'];
			
			for ($i = 0; $i < $params['actual_lifes']; $i++) {
				$playerCards[] = array_pop($cards);
			}
			
			$params['hand_cards'] = serialize($playerCards);
			$GLOBALS['db']->update(self::$gamePlayerTable, $params, 'game = ' . intval($game['id']) . ' AND user = ' . intval($player['user']));
			
			$j++;
		}
		
		$params = array(
			'draw_pile' => serialize($cards),
			'game_start' => time(),
			'status' => Game::GAME_STATUS_STARTED,
		);
		
		$GLOBALS['db']->update(self::$table, $params, 'id = ' . intval($game['id']));
		
		
		// todo zratat matrix
		
		
		return 'Štart';
		
	}
}