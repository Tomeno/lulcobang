<?php

class GameUtils {

	protected static $table = 'game';
	protected static $playerTable = 'player';
	
	public static function create() {
		$room = intval($_GET['id']);
		
		$query = 'SELECT count(*) AS pocet FROM ' . self::$table . ' WHERE room = ' . $room . ' AND status IN (' . Game::GAME_STATUS_CREATED . ', ' . Game::GAME_STATUS_STARTED . ')';
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
	
	public static function addPlayer($game, $user) {
		if ($game && $game['status'] == 0) {
			$gameId =  intval($game['id']);
			$query = 'SELECT count(*) AS pocet FROM ' . self::$playerTable . ' WHERE game = ' . $gameId . ' AND user = ' . intval($user);
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
				$GLOBALS['db']->insert(self::$playerTable, $params);
				return ' sa pridal k hre';
			}
		}
		return ' sa nemôže zapojiť do hry, pretože v tejto miestnosti sa nehrá žiadna hra.';
	}
	
	private static function getPosition($game) {
		$query = 'SELECT MAX(position) AS max_position FROM ' . self::$playerTable . ' WHERE game = ' . intval($game);
		$position = $GLOBALS['db']->fetchFirst($query);
		
		return $position['max_position'] + 1;
	}
	
	public function start($game) {
		$players = $game['players'];
		
		$roleRepository = new RoleRepository();
		$characterRepository = new CharakterRepository();
		$cardRepository = new CardRepository();
		
		$roles = $roleRepository->getRoles(count($players));
		shuffle($roles);
		
		$characters = $characterRepository->getAll();
		shuffle($characters);
		
		$cards = $cardRepository->getCardIds();
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
			$GLOBALS['db']->update(self::$playerTable, $params, 'game = ' . intval($game['id']) . ' AND user = ' . intval($player['user']['id']));
			
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

?>