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
		$this->addAdditionalField('draw_pile', $drawPileCards);
		
		$throwPile = unserialize($game['throw_pile']);
		$throwPileCards = array();
		if ($throwPile) {
			foreach ($throwPile as $cardId) {
				$throwPileCards[] = $cardRepository->getOneById($cardId);
			}
		}
		$this->addAdditionalField('throw_pile', $throwPileCards);
		
		$playerRepository = new PlayerRepository();
		$players = $playerRepository->getByGame($game['id']);
		$this->addAdditionalField('players', $players);
	}
	
	public function getTopThrowPile() {
		return array_pop($this['throw_pile']);
	}
	
	public function getTopDrawPile() {
		return array_pop($this['draw_pile']);
	}
	
	public function getPlayerOnTurn() {
		foreach ($this['players'] as $player) {
			if ($player['position'] == $this['turn']) {
				return $player;
			}
		}
		return null;
	}
	
	public function getPlayerByUsername($username) {
		foreach ($this['players'] as $player) {
			if ($player['user']['username'] == $username) {
				return $player;
			}
		}
		return null;
	}
}

?>