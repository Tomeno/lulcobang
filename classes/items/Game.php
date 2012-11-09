<?php

class Game extends Item {
	
	const GAME_STATUS_CREATED = 0;

	const GAME_STATUS_INITIALIZED = 1;
	
	const GAME_STATUS_STARTED = 2;
	
	const GAME_STATUS_ENDED = 3;
	
	const MINIMUM_PLAYERS_COUNT = 2;

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
		$this->setAdditionalField('draw_pile', $drawPileCards);
		
		$throwPile = unserialize($game['throw_pile']);
		$throwPileCards = array();
		if ($throwPile) {
			foreach ($throwPile as $cardId) {
				$throwPileCards[] = $cardRepository->getOneById($cardId);
			}
		}
		$this->setAdditionalField('throw_pile', $throwPileCards);
		
		$playerRepository = new PlayerRepository();
		$players = $playerRepository->getByGame($game['id']);
		$this->setAdditionalField('players', $players);
		
		$this->setAdditionalField('matrix', unserialize($game['distance_matrix']));
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

	/**
	 * gets the player on turn - he can draw cards, bangs the enemies etc
	 *
	 * @return	Player|NULL
	 */
	public function getPlayerOnTurn() {
		foreach ($this->getPlayers() as $player) {
			if ($player['position'] == $this['turn']) {
				return $player;
			}
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
		if ($this['inter_turn']) {
			foreach ($this->getPlayers() as $player) {
				if ($player['position'] == $this['inter_turn']) {
					return $player;
				}
			}
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
}

?>