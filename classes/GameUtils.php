<?php

class GameUtils {

	protected static $table = 'game';
	protected static $playerTable = 'player';
	
	public static function create() {
		$room = intval($_GET['id']);
		
		$query = 'SELECT count(*) AS pocet FROM ' . self::$table . ' WHERE room = ' . $room . ' AND status IN (' . Game::GAME_STATUS_CREATED . ', ' . Game::GAME_STATUS_STARTED . ')';
		$game = $GLOBALS['db']->fetchFirst($query);
		if ($game['pocet'] > 0) {
			return false;
		}
		else {
			$params = array(
				'room' => $room,
			);
			$GLOBALS['db']->insert(self::$table, $params);
			return true;
		}
	}
	
	public static function save($game) {
		$drawPile = array();
		foreach ($game['draw_pile'] as $card) {
			$drawPile[] = $card['id'];
		}
		
		$throwPile = array();
		foreach ($game['throw_pile'] as $card) {
			$throwPile[] = $card['id'];
		}
		
		$params = array(
			'draw_pile' => serialize($drawPile),
			'throw_pile' => serialize($throwPile),
		);
		$GLOBALS['db']->update(self::$table, $params, 'id = ' . intval($game['id']));
		
		foreach ($game['players'] as $player) {
			
			$handCards = array();
			foreach ($player['hand_cards'] as $card) {
				$handCards[] = $card['id'];
			}
			
			$tableCards = array();
			foreach ($player['table_cards'] as $card) {
				$tableCards[] = $card['id'];
			}
			
			$params = array(
				'hand_cards' => serialize($handCards),
				'table_cards' => serialize($tableCards),
			);
			$GLOBALS['db']->update(self::$playerTable, $params, 'id = ' . intval($player['id']));
		}
		
	}
	
	public static function addPlayer($game, $user) {
		if ($game) {
			if ($game['status'] == 0) {
				$gameId =  intval($game['id']);
				$query = 'SELECT count(*) AS pocet FROM ' . self::$playerTable . ' WHERE game = ' . $gameId . ' AND user = ' . intval($user);
				$userCount = $GLOBALS['db']->fetchFirst($query);
				if ($userCount['pocet'] > 0) {
					return 2;
				}
				else {
					$pos = intval(self::getPosition($gameId));
					$params = array(
						'game' => $gameId,
						'user' => intval($user),
						'position' => $pos,
						'seat' => $pos,
					);
					$GLOBALS['db']->insert(self::$playerTable, $params);
					return 1;
				}
			}
			elseif ($game['status'] == 1) {
				return 3;
			}
		}
		return 4;
	}
	
	private static function getPosition($game) {
		$query = 'SELECT MAX(position) AS max_position FROM ' . self::$playerTable . ' WHERE game = ' . intval($game);
		$position = $GLOBALS['db']->fetchFirst($query);
		
		return $position['max_position'] + 1;
	}
	
	public function start($game) {
		$players = $game['players'];
		
		if (count($players) >= 2) {
			
			// TODO nemozno spustit hru ked uz je spustena
			
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
				
				if ($roles[$j]['type'] == Role::SHERIFF) {
					self::setTurn($game, $player['position']);
					$params['phase'] = 1;
				}
				
				$params['charakter'] = $characters[$j]['id'];
				$params['actual_lifes'] = $characters[$j]['lifes'];
				
				for ($i = 0; $i < $params['actual_lifes']; $i++) {
					$playerCards[] = array_pop($cards);
				}
				
				$params['hand_cards'] = serialize($playerCards);
				$params['table_cards'] = serialize(array());
				$GLOBALS['db']->update(self::$playerTable, $params, 'game = ' . intval($game['id']) . ' AND user = ' . intval($player['user']['id']));
				
				$j++;
			}
			
			$params = array(
				'draw_pile' => serialize($cards),
				'throw_pile' => serialize(array()),
				'game_start' => time(),
				'status' => Game::GAME_STATUS_STARTED,
			);
			
			$GLOBALS['db']->update(self::$table, $params, 'id = ' . intval($game['id']));
			
			self::countMatrix($game);
			
			return 'Štart';
		}
		else {
			return 'Do hry musia byť zapojení aspoň 2 hráči.';
		}
	}
	
	public static function countMatrix($game) {
		
		$playerRepository = new PlayerRepository();
		$players = $playerRepository->getLivePlayersByGame($game['id']);
		$playersCount = count($players);
		
		$matrix = array();
		foreach ($players as $player1) {
			foreach ($players as $player2) {
				if ($player1['id'] == $player2['id']) {
					$matrix[$player1['id']][$player2['id']] = 0;
				}
				else {
					$arg1 = $player1['position'] - $player2['position'];
					$arg2 = $player2['position'] - $player1['position'];
					
					$arg1 = $arg1 < 0 ? $arg1 + $playersCount : $arg1;
					$arg2 = $arg2 < 0 ? $arg2 + $playersCount : $arg2;
					
					$distance = min($arg1, $arg2);
					
					if ($player2->getHasMustangOnTheTable()) {
						$distance++;
					}
					if ($player2->getIsPaulRegret()) {
						$distance++;
					}
					if ($player1->getHasAppaloosaOnTheTable) {
						$distance--;
					}
					if ($player1->getIsRoseDoolan()) {
						$distance--;
					}
					
					$matrix[$player1['id']][$player2['id']] = $distance > 0 ? $distance : 0;
				}
			}
		}
		
		$params = array(
			'distance_matrix' => serialize($matrix)
		);
		$GLOBALS['db']->update(self::$table, $params, 'id = ' . intval($game['id']));
	}
	
	/**
	 * @todo tato metoda zmeni positions po umreti hraca
	 *
	 * @param Game $game
	 */
	public static function changePositions($game) {
		
	}
	
	public static function setTurn($game, $position) {
		$params = array(
			'turn' => intval($position),
		);
		$GLOBALS['db']->update(self::$table, $params, 'id = ' . intval($game['id']));
	}
	
	public static function setInterTurn($game, $position) {
		$params = array(
			'inter_turn' => intval($position),
		);
		$GLOBALS['db']->update(self::$table, $params, 'id = ' . intval($game['id']));
	}
	
	public static function getCards($game, $player, $count) {
		foreach ($game['players'] as &$gamePlayer) {
			if ($gamePlayer['id'] == $player['id']) {
				if (self::checkTurn($game, $player)) {
					$playerCards = $gamePlayer['hand_cards'];
					$drawPile = $game['draw_pile'];
					$throwPile = $game['throw_pile'];
					
					for ($i = 0; $i < $count; $i++) {
						if (count($drawPile) == 0) {
							$drawPile = array_reverse($throwPile);
							$throwPile = array();
						}
						$playerCards[] = array_pop($drawPile);
					}
					$gamePlayer['hand_cards'] = $playerCards;
					$game['draw_pile'] = $drawPile;
					$game['throw_pile'] = $throwPile;
					
					self::save($game);
					
					return ' potiahol karty';
				}
				return ' nemôže ťahať, lebo nie je na rade.';
			}
		}
		return ' nehrá túto hru.';
	}
	
	public static function throwCards($game, $player, $card) {
		foreach ($game['players'] as &$gamePlayer) {
			if ($gamePlayer['id'] == $player['id']) {
				if (self::checkTurn($game, $player)) {
					$playerCards = $gamePlayer['hand_cards'];
					$throwPile = $game['throw_pile'];
					$newPlayerCards = array();
					foreach ($playerCards as $playerCard) {
						if ($playerCard['id'] == $card['id']) {
							$throwPile[] = $playerCard;
						}
						else {
							$newPlayerCards[] = $playerCard;
						}
					}
					
					$gamePlayer['hand_cards'] = $newPlayerCards;
					$game['throw_pile'] = $throwPile;
					
					self::save($game);
					
					return ' vyhodil kartu ' . $card['title'];
					
				}
				return ' nemôže vyhodiť karty, lebo nie je na rade.';
			}
		}
		return ' nehrá túto hru.';
	}
	
	public static function checkTurn($game, $player) {
		if ($game['inter_turn']) {
			if ($game['inter_turn'] == $player['position']) {
				return true;
			}
		}
		else {
			if ($game['turn'] == $player['position']) {
				return true;
			}
		}
		return false;
	}
	
	public static function setPhase($game, $player, $phase) {
		$params = array(
			'phase' => intval($phase),
		);
		$GLOBALS['db']->update(self::$playerTable, $params, 'game = ' . intval($game['id']) . ' AND id = ' . intval($player['id']));
	}
	
	public static function putOnTable($game, $playerFrom, $card, $playerTo = null) {
		$playerTo = $playerTo ? $playerTo : $playerFrom;
		$ok = false;
		foreach ($game['players'] as &$gamePlayer) {
			if ($gamePlayer['id'] == $playerFrom['id']) {
				$playerCards = $gamePlayer['hand_cards'];
				$newPlayerCards = array();
				foreach ($playerCards as $playerCard) {
					if ($playerCard['id'] != $card['id']) {
						$newPlayerCards[] = $playerCard;
					}
				}
				$gamePlayer['hand_cards'] = $newPlayerCards;
				$ok = true;
				break;
			}
		}
		foreach ($game['players'] as &$player) {
			if ($player['id'] == $playerTo['id']) {
				$tableCards = $player['table_cards'];
				$tableCards[] = $card;
				$player['table_cards'] = $tableCards;
				if ($ok) {
					self::save($game);
				}
				return $ok;
			}
		}
		return $ok;
	}
}

?>