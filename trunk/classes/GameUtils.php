<?php

class GameUtils {

	protected static $table = 'game';
	protected static $playerTable = 'player';
	protected static $seats = array(1, 5, 3, 7, 2, 6, 4, 8);
	protected static $positions = array(1, 2, 3, 4, 5, 6, 7, 8);

	public static function getPosition(Game $game) {
		$playerRepository = new PlayerRepository();
		$playersCount = $playerRepository->getCountByGame(intval($game['id']));
		return $playersCount;
	}

	public static function getSeatOnPosition($position) {
		if (isset(self::$seats[$position])) {
			return self::$seats[$position];
		} else {
			throw new Exception('Seat on position ' . $position . ' doesn\'t exist', 1327605760);
		}
	}

	public static function checkUserInGame($user, $game) {
		$playerRepository = new PlayerRepository();
		$playerRepository->addAdditionalWhere(array('column' => 'game', 'value' => $game['id']));
		$playerRepository->addAdditionalWhere(array('column' => 'user', 'value' => $user['id']));
		return $playerRepository->getCountAll();
	}


	public static function save($game) {
		echo 'GameUtils::save';
		exit();
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
		DB::update(self::$table, $params, 'id = ' . intval($game['id']));
		
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
			DB::update(self::$playerTable, $params, 'id = ' . intval($player['id']));
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
					$matrix[$player1['user']['username']][$player2['user']['username']] = 0;
				} else {
					$arg1 = $player1['position'] - $player2['position'];
					$arg2 = $player2['position'] - $player1['position'];
					
					$arg1 = $arg1 < 0 ? $arg1 + $playersCount : $arg1;
					$arg2 = $arg2 < 0 ? $arg2 + $playersCount : $arg2;
					
					$distance = min($arg1, $arg2);
					
					if ($player2->getHasMustangOnTheTable()) {
						// TODO ak je tu belle stark ci ako sa vola, tak v jej tahu nemaju vplyv modre karty
						// takze ani mustang nebude platit, a ani barel a dalsie
						$distance++;
					}
					if ($player2->getHasHideoutOnTheTable()) {
						// TODO ak je tu belle stark ci ako sa vola, tak v jej tahu nemaju vplyv modre karty
						// takze ani hideout nebude platit, a ani barel a dalsie
						$distance++;
					}
					if ($player2->getCharacter()->getIsPaulRegret()) {
						$distance++;
					}
					if ($player1->getHasAppaloosaOnTheTable()) {
						$distance--;
					}
					if ($player1->getHasSilverOnTheTable()) {
						$distance--;
					}
					if ($player1->getCharacter()->getIsRoseDoolan()) {
						$distance--;
					}
					
					$matrix[$player1['user']['username']][$player2['user']['username']] = $distance > 1 ? $distance : 1;
				}
			}
		}
		return $matrix;
	}
	
	/**
	 * @todo tato metoda zmeni positions po umreti hraca
	 *
	 * @param Game $game
	 */
	public static function changePositions($game) {
		$i = 1;
		// foreachneme positions tak aby sme sli do kruhu a postupne zistime ci na tychto miestach sedia nejaki hraci
		foreach (self::$positions as $seat) {
			$player = self::getPlayerOnSeat($game, $seat);
			
			if ($player) {
				if ($player['actual_lifes'] > 0) {
					$pos = $i;
					$i++;
				} else {
					$pos = 0;
				}
				$player['position'] = $pos;
				$player->save();
			}
		}
		return $game->save(TRUE);
	}
	
	protected static function getPlayerOnSeat($game, $seat) {
		foreach ($game->getAdditionalField('players') as $player) {
			if ($player['seat'] == $seat) {
				return $player;
			}
		}
		return null;
	}
	
	public static function setTurn($game, $position) {
		echo 'GameUtils::setTurn';
		exit();
		$params = array(
			'turn' => intval($position),
		);
		DB::update(self::$table, $params, 'id = ' . intval($game['id']));
	}
	
//	public static function getPlayerOnNextPosition($game, $actualPlayer = NULL) {
//		$playerRepository = new PlayerRepository();
//		$players = $playerRepository->getLivePlayersByGame($game['id']);
//		$playersCount = count($players);
//
//		if ($actualPlayer === NULL) {
//			$actualPlayer = $playerRepository->getOneByIdAndGame($game['turn'], $game['id']);
//		}
//		$next = $actualPlayer['position'] + 1;
//		
//		$nextPosition = $next <= $playersCount ? $next : $next - $playersCount;
//		return $playerRepository->getOneByPositionAndGame($nextPosition, $game['id']);
//	}
	
	
	
	public static function getPlayerOnNextPosition($game, $actualPlayer = NULL) {
		$playerRepository = new PlayerRepository();
		if ($actualPlayer === NULL) {
			$actualPlayer = $playerRepository->getOneByIdAndGame($game['turn'], $game['id']);
		}
		
		$findingStarted = FALSE;
		$nextPlayer = NULL;
		foreach (self::$positions as $seat) {
			if ($findingStarted === TRUE) {
				$player = self::getPlayerOnSeat($game, $seat);
				if ($player) {
					if ($player['actual_lifes'] > 0) {
						$nextPlayer = $player;
						break;
					}
				}
			}
			
			if ($actualPlayer['seat'] == $seat) {
				$findingStarted = TRUE;
			}
		}
		
		if ($nextPlayer === NULL) {
			foreach (self::$positions as $seat) {
				$player = self::getPlayerOnSeat($game, $seat);
				if ($player) {
					if ($player['actual_lifes'] > 0) {
						$nextPlayer = $player;
						break;
					}
				}
			}
		}
		
		return $nextPlayer;
	}
	
	
	
	
	
	public static function getNextPosition($game, $actualPosition = 0) {
		echo 'GameUtils::getNextPosition';
		exit();
		$playerRepository = new PlayerRepository();
		$players = $playerRepository->getLivePlayersByGame($game['id']);
		$playersCount = count($players);

		if ($actualPosition) {
			$next = $actualPosition + 1;
		} else {
			$next = $game['turn'] + 1;
		}
		
		return $next <= $playersCount ? $next : $next - $playersCount;
	}
	
	public static function setInterTurn($game, $position, $reason = '') {
	throw new Exception('GameUtils::setInterTurn remove this function');
		$params = array(
			'inter_turn' => intval($position),
			'inter_turn_reason' => addslashes($reason),
		);
		DB::update(self::$table, $params, 'id = ' . intval($game['id']));
	}
	
	public static function getCards($game, $player, $count) {
		echo 'dostal som sa sem';exit();
		foreach ($game->getAdditionalField('players') as $gamePlayer) {
			if ($gamePlayer['id'] == $player['id']) {
				//if (self::checkTurn($game, $player)) {
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
				//}
				//return ' nemôže ťahať, lebo nie je na rade.';
			}
		}
		return ' nehrá túto hru.';
	}

	/**
	 * draw cards from draw pile of a $game
	 *
	 * @param	Game	$game
	 * @param	integer	$count
	 * @return	array	- IDs array of drawn cards
	 */
	public static function drawCards(Game $game, $count) {
		$drawPile = $game->getDrawPile();
		$drawnCards = array();
		for ($i = 0; $i < $count; $i++) {
			$card = array_pop($drawPile);
			$drawnCards[] = $card['id'];

			if (empty($drawPile)) {
				$drawPile = $game->getThrowPile();
				shuffle($drawPile);
				$game->setAdditionalField('throw_pile', array());
				$game['throw_pile'] = serialize(array());
			}
		}
		
		$game->setAdditionalField('draw_pile', $drawPile);
		$newDrawPile = array();
		foreach ($drawPile as $card) {
			$newDrawPile[] = $card['id'];
		}
		
		$game['draw_pile'] = serialize($newDrawPile);
		$game->save();

		return $drawnCards;
	}

	/**
	 * used for throwing cards from player to throw_pile
	 *
	 * @param	Game	$game
	 * @param	Player	$player
	 * @param	array<Card>	$thrownCards
	 * @param	string	$place
	 * @return	array
	 */
	public static function throwCards(Game $game, Player $player = NULL, array $thrownCards = array(), $place = 'hand') {
		$thrownCardsIds = array();
		foreach ($thrownCards as $card) {
			$thrownCardsIds[] = $card['id'];
		}

		$throwPile = unserialize($game['throw_pile']);
		if ($player !== NULL) {
			$playerCards = unserialize($player[$place . '_cards']);
			$newPlayerCards = array();
			foreach ($playerCards as $playerCard) {
				if (in_array($playerCard, $thrownCardsIds)) {
					$throwPile[] = $playerCard;
				} else {
					$newPlayerCards[] = $playerCard;
				}
			}
			$player[$place . '_cards'] = serialize($newPlayerCards);
			$player = $player->save(TRUE);
		} else {
			$throwPile = array_merge($throwPile, $thrownCardsIds);
		}
		$game['throw_pile'] = serialize($throwPile);
		
		$game = $game->save(TRUE);
		return array('game' => $game, 'player' => $player);
	}

	/**
	 * used for putting cards from one player to another to the table, or to the hand, or to the waiting box
	 *
	 * @param	Game	$game
	 * @param	array	$movedCards
	 * @param	Player	$playerFrom
	 * @param	string	$placeTo
	 * @param	Player	$playerTo
	 * @param	Player	$placeFrom
	 * @return	array
	 */
	public static function moveCards(Game $game, array $movedCards, Player $playerFrom, $placeTo = 'table', Player $playerTo = NULL, $placeFrom = 'hand') {
		$samePlayer = FALSE;
		if ($playerTo === NULL) {
			$playerTo = $playerFrom;
			$samePlayer = TRUE;
		}

		$movedCardsIds = array();
		foreach ($movedCards as $card) {
			$movedCardsIds[] = $card['id'];
		}

		$playerFromCards = unserialize($playerFrom[$placeFrom . '_cards']);
		$playerToCards = unserialize($playerTo[$placeTo . '_cards']);

		$newPlayerHandCards = array();
		foreach ($playerFromCards as $card) {
			if (in_array($card, $movedCardsIds)) {
				$playerToCards[] = $card;
			} else {
				$newPlayerHandCards[] = $card;
			}
		}

		$playerFrom[$placeFrom . '_cards'] = serialize($newPlayerHandCards);
		if ($samePlayer === TRUE) {
			$playerFrom[$placeTo . '_cards'] = serialize($playerToCards);
		} else {
			$playerTo[$placeTo . '_cards'] = serialize($playerToCards);
			$playerTo = $playerTo->save(TRUE);
		}
		$playerFrom = $playerFrom->save(TRUE);
		return array('game' => $game, 'playerFrom' => $playerFrom, 'playerTo' => $playerTo);
	}

	public static function checkTurn($game, $player) {
		echo 'GameUtils:checkTurn';exit();
		if ($game['status'] == Game::GAME_STATUS_STARTED) {
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
		}
		return false;
	}
	
	public static function setPhase($game, $player, $phase) {
		echo 'GameUtils::setPhase';exit();
		$params = array(
			'phase' => intval($phase),
		);
		DB::update(self::$playerTable, $params, 'game = ' . intval($game['id']) . ' AND id = ' . intval($player['id']));
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
	
	public static function getClassForPosition($actualPlayer, $player) {
		$class = (($player['seat'] - $actualPlayer['seat']) % 8) + 1;
		return $class < 1 ? $class + 8 : $class;
	}
}

?>