<?php

class Player extends Item {
	
	public function __construct($player) {
		parent::__construct($player);
		
		$userRepository = new UserRepository();
		$user = $userRepository->getOneById($player['user']);
		$this->addAdditionalField('user', $user);
		
		$roleRepository = new RoleRepository();
		$role = $roleRepository->getOneById($player['role']);
		$this->addAdditionalField('role', $role);
		
		$characterRepository = new CharacterRepository();
		$character = $characterRepository->getOneById($player['charakter']);
		$this->addAdditionalField('character', $character);
		
		$cardRepository = new CardRepository();
		
		$handCardsId = unserialize($player['hand_cards']);
		$handCards = array();
		if ($handCardsId) {
			foreach ($handCardsId as $cardId) {
				$handCards[] = $cardRepository->getOneById($cardId);
			}
		}
		$this->addAdditionalField('hand_cards', $handCards);
		
		$tableCardsId = unserialize($player['table_cards']);
		$tableCards= array();
		if ($tableCardsId) {
			foreach ($tableCardsId as $cardId) {
				$tableCards[] = $cardRepository->getOneById($cardId);
			}
		}
		$this->addAdditionalField('table_cards', $tableCards);
	}
	
	public function __call($methodName, $arguments) {
		if (substr($methodName, 0, 5) === 'getIs') {
			$character = strtolower(str_replace('getIs', '', $methodName));
			$realCharacter = strtolower(str_replace(' ', '', $this['charakter']['name']));
			if ($character == $realCharacter) {
				return true;
			}
			return false;
		}
		if (substr($methodName, 0, 6) === 'getHas') {
			$place = '';
			if (strpos($methodName, 'OnTheTable')) {
				$place = 'table';
			}
			elseif (strpos($methodName, 'OnHand')) {
				$place = 'hand';
			}
			if ($place) {
				$cardType = str_replace(array('getHas', 'OnTheTable', 'OnHand'), '', $methodName);
				return $this->hasCardType($cardType, $place);
			}
		}
		throw new Exception('Method ' . $methodName . ' doesn\'t exist');
	}
	
	/**
	 * checks if player has card type
	 *
	 * @param string $cardType
	 * @param string $place
	 * @return Card if has card | false if has not | 0 if method doesn't exist
	 */
	protected function hasCardType($cardType, $place = 'table') {
		$methodName = 'getIs' . $cardType;
		if (method_exists('Card', $methodName)) {
			foreach ($this->getAdditionalField($place . '_cards') as $card) {
				if ($card->$methodName()) {
					return $card;
				}
			}
			return false;
		}
		return 0;
	}
	
	public function setPhase($phase) {
		$GLOBALS['db']->update('player', array('phase' => $phase), 'id = ' . intval($this['id']));
	}
	
	/**
	 * checks if player can pass
	 *
	 * @todo special ability for character ...
	 * 
	 * @return boolean
	 */
	public function getCanPass() {
		if ($this['actual_lifes'] >= count($this->getAdditionalField('hand_cards'))) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function getDostrel() {
		$card = $this->getHasGun();
		if ($card) {
			if ($card->getIsSchofield()) {
				return 2;
			}
			elseif ($card->getIsRemington()) {
				return 3;
			}
			elseif ($card->getIsCarabina()) {
				return 4;
			}
			elseif ($card->getIsWinchester()) {
				return 5;
			}
		}
		return 1;
	}
	
	public function getHasGun() {
		$guns = Card::getGuns();
		foreach ($guns as $gun) {
			$methodName = 'getHas' . ucfirst($gun) . 'OnTheTable';
			$card = $this->$methodName();
			if ($card) {
				return $card;
			}
		}
	}
	
	public function takeLife() {
		$newLifes = $this['actual_lifes'] - 1;
		$GLOBALS['db']->update('player', array('actual_lifes' => $newLifes), 'id = ' . intval($this['id']));
		return $newLifes;
	}
	
	public function addLife() {
		$character = $this->getAdditionalField('character');
		$maxLifes = $character['lifes'];
		$role = $this->getAdditionalField('role');
		$maxLifes = $role['type'] == Role::SHERIFF ? $maxLifes + 1 : $maxLifes;
		if ($this['actual_lifes'] < $maxLifes) {
			$newLifes = $this['actual_lifes'] + 1;
			$GLOBALS['db']->update('player', array('actual_lifes' => $newLifes), 'id = ' . intval($this['id']));
			return $newLifes;
		}
		return false;
	}
	
	public function setUseBang($useBang) {
		$GLOBALS['db']->update('player', array('use_bang' => intval($useBang)), 'id = ' . intval($this['id']));
	}
	
	/*
	public function getHasMustangOnTheTable() {
		return $this->hasCardType(Card::MUSTANG);
	}
	
	public function getHasAppaloosaOnTheTable() {
		return $this->hasCardType(Card::APPALOOSA);
	}
	
	public function getHasBarelOnTheTable() {
		return $this->hasCardType(Card::BAREL);
	}
	
	public function getHasDostavnikOnHand() {
		return $this->hasCardType(Card::DOSTAVNIK, 'hand');
	}
	
	public function getHasWellsFargoOnHand() {
		return $this->hasCardType(Card::WELLS_FARGO, 'hand');
	}
	
	public function getHasAppaloosaOnHand() {
		return $this->hasCardType(Card::APPALOOSA, 'hand');
	}
	
	public function getHasMustangOnHand() {
		return $this->hasCardType(Card::MUSTANG, 'hand');
	}*/
}

?>