<?php

class Game extends Item {
	
	const GAME_STATUS_CREATED = 0;

	const GAME_STATUS_INITIALIZED = 1;
	
	const GAME_STATUS_STARTED = 2;
	
	const GAME_STATUS_ENDED = 3;
	
	const MINIMUM_PLAYERS_COUNT = 2;

	const GAME_SET_BANG = 1;
	
	const GAME_SET_DODGE_CITY = 2;
	
	const GAME_SET_HIGH_NOON = 3;
	
	const GAME_SET_A_FISTFUL_OF_CARDS = 4;

	const GAME_SET_WILD_WEST_SHOW = 5;
	
	public function __construct($game) {
		parent::__construct($game);
		
		$cardRepository = new CardRepository(TRUE);
		
		$drawPile = unserialize($game['draw_pile']);
		$drawPileCards = array();
		if ($drawPile) {
			foreach ($drawPile as $cardId) {
				$drawPileCards[] = $cardRepository->getOneById($cardId);
			}
		}
		$this->setAdditionalField('draw_pile', $drawPileCards);
		
		$throwPile = unserialize($game['throw_pile']);
		$throwPileCards = array();
		if ($throwPile) {
			foreach ($throwPile as $cardId) {
				$throwPileCards[] = $cardRepository->getOneById(intval($cardId));
			}
		}
		$this->setAdditionalField('throw_pile', $throwPileCards);
		
		$playerRepository = new PlayerRepository();
		$players = $playerRepository->getByGame($game['id']);
		$this->setAdditionalField('players', $players);
		
		$this->setAdditionalField('matrix', unserialize($game['distance_matrix']));
		
		$gameSets = unserialize($game['game_sets']);
		if (in_array(3, $gameSets)) {
			$isHighNoon = TRUE;
		} else {
			$isHighNoon = FALSE;
		}
		$this->setAdditionalField('isHighNoon', $isHighNoon);
		
		if ($isHighNoon) {
			$highNoonPile = unserialize($game['high_noon_pile']);
			$highNoonRepository = new HighNoonRepository(TRUE);
			$highNoonPileCards = array();
			if ($highNoonPile) {
				foreach ($highNoonPile as $cardId) {
					$highNoonPileCards[] = $highNoonRepository->getOneById(intval($cardId));
				}
			}
			$this->setAdditionalField('highNoonPile', $highNoonPileCards);
			
			if ($game['high_noon']) {
				$highNoonRepository = new HighNoonRepository();
				$highNoon = $highNoonRepository->getOneById(intval($game['high_noon']));
				$this->setAdditionalField('highNoon', $highNoon);
			}
		}
	}
	
		
	public function __call($methodName, $arguments) {
		if (substr($methodName, 0, 7) === 'getIsHN') {
			if ($this['high_noon']) {
				$realHighNoonCard = $this->getAdditionalField('highNoon');
				$realHighNoon = strtolower(str_replace(array(' ', '\''), '', $realHighNoonCard['title']));
				$highNoon = strtolower(str_replace('getIsHN', '', $methodName));

				if ($realHighNoon == $highNoon) {
					return TRUE;
				}
			}
			return FALSE;
		}
	}

	public function getThrowPile() {
		return $this->getAdditionalField('throw_pile');
	}
	
	public function getTopThrowPile() {
		return array_pop($this->getThrowPile());
	}

	public function getDrawPile() {
		return $this->getAdditionalField('draw_pile');
	}

	public function getTopDrawPile() {
		return array_pop($this->getDrawPile());
	}
	
	public function getPlayers() {
		return $this->getAdditionalField('players');
	}
	
	public function getAlivePlayers() {
		$players = $this->getPlayers();
		$alivePlayers = array();
		foreach ($players as $player) {
			if ($player->getIsAlive()) {
				$alivePlayers[] = $player;
			}
		}
		return $alivePlayers;
	}
	
	public function getAlivePlayersCount() {
		return count($this->getAlivePlayers());
	}

	/**
	 * gets the player on turn - he can draw cards, bangs the enemies etc
	 *
	 * @return	Player|NULL
	 */
	public function getPlayerOnTurn() {
		if ($this['turn']) {
			$playerRepository = new PlayerRepository();
			return $playerRepository->getOneById($this['turn']);
		}
		return NULL;
	}

	/**
	 * gets the player on move if inter_turn is set - this player is under attack or has some other reason for inter_turn
	 * if inter_turn is not set, returns player on turn
	 * 
	 * @return	Player|NULL
	 */
	public function getPlayerOnMove() {
		$playerRepository = new PlayerRepository();
		if ($this['inter_turn']) {
			return $playerRepository->getOneById($this['inter_turn']);
		} else {
			return $this->getPlayerOnTurn();
		}
	}
	
	public function getPlayerByUsername($username) {
		foreach ($this->getPlayers() as $player) {
			if ($player['user']['username'] == $username) {
				return $player;
			}
		}
		return null;
	}

	public function getRoomObject() {
		$roomRepository = new RoomRepository();
		$room = $roomRepository->getOneById($this['room']);
		return $room;
	}
	
	public function getMatrix() {
		return $this->getAdditionalField('matrix');
	}
	
	public function getDistance($playerFrom, $playerTo) {
		$matrix = $this->getMatrix();
		if (isset($matrix[$playerFrom][$playerTo])) {
			return $matrix[$playerFrom][$playerTo];
		}
		return FALSE;
	}

	public function getIsHighNoon() {
		return $this->getAdditionalField('isHighNoon');
	}
	
	public function getHighNoonActualCard() {
		return $this->getAdditionalField('highNoon');
	}
	
	public function getHighNoonPile() {
		return $this->getAdditionalField('highNoonPile');
	}
	
	public function getTopHighNoonPile() {
		return array_pop($this->getHighNoonPile());
	}
	
	public function getAliveVicePlayers() {
		$viceList = array();
		foreach ($this->getAlivePlayers() as $player) {
			if ($player->getRoleObject()->getIsVice()) {
				$viceList[] = $player;
			}
		}
		return $viceList;
	}
}

?>