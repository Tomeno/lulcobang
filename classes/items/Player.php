<?php

class Player extends Item {

	const PHASE_NONE = 0;
	const PHASE_PREDRAW = 1;
	const PHASE_DYNAMITE = 2;
	const PHASE_JAIL = 3;
	const PHASE_DRAW = 4;
	const PHASE_PLAY = 5;
	const PHASE_UNDER_ATTACK = 6;
	
	public function __construct($player) {
		parent::__construct($player);
		
		$userRepository = new UserRepository();
		$user = $userRepository->getOneById($player['user']);
		$this->setAdditionalField('user', $user);
		
		$roleRepository = new RoleRepository();
		$role = $roleRepository->getOneById($player['role']);
		$this->setAdditionalField('role', $role);
		
		$characterRepository = new CharacterRepository();
		$character = $characterRepository->getOneById($player['charakter']);
		$this->setAdditionalField('character', $character);
		
		$cardRepository = new CardRepository();
		
		$handCardsId = unserialize($player['hand_cards']);
		$handCards = array();
		if ($handCardsId) {
			$cardRepository->addOrderBy(array('card_base_type' => 'ASC'));
			$handCards = $cardRepository->getById($handCardsId);
		}
		$this->setAdditionalField('hand_cards', $handCards);
		
		$cardRepository = new CardRepository();

		$tableCardsId = unserialize($player['table_cards']);
		$tableCards= array();
		if ($tableCardsId) {
			$cardRepository->addOrderBy(array('card_base_type' => 'ASC'));
			$tableCards = $cardRepository->getById($tableCardsId);
		}
		$this->setAdditionalField('table_cards', $tableCards);

		$waitCardsId = unserialize($player['wait_cards']);
		$waitCards= array();
		if ($waitCardsId) {
			$cardRepository->addOrderBy(array('card_base_type' => 'ASC'));
			$waitCards = $cardRepository->getById($waitCardsId);
		}
		$this->setAdditionalField('wait_cards', $waitCards);
	}
	
	public function __call($methodName, $arguments) {
		if (substr($methodName, 0, 6) === 'getHas') {
			$place = '';
			if (strpos($methodName, 'OnTheTable')) {
				$place = 'table';
			} elseif (strpos($methodName, 'OnHand')) {
				$place = 'hand';
			} elseif (strpos($methodName, 'OnWait')) {
				$place = 'wait';
			}

			if ($place) {
				$cardType = str_replace(array('getHas', 'OnTheTable', 'OnHand', 'OnWait'), '', $methodName);
				$cardType = Utils::createLowercaseFromText($cardType);
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
		$methodName = 'getIs' . ucfirst($cardType);
		if (method_exists('Card', $methodName)) {
			foreach ($this->getAdditionalField($place . '_cards') as $card) {
				if ($card->$methodName()) {
					return $card;
				}
			}
			return NULL;
		}
		return FALSE;
	}

	public function getCharacter() {
		return $this->getAdditionalField('character');
	}

	public function getRoleObject() {
		return $this->getAdditionalField('role');
	}

	public function getUser() {
		return $this->getAdditionalField('user');
	}

	public function getHandCards() {
		return $this->getAdditionalField('hand_cards');
	}

	public function getTableCards() {
		return $this->getAdditionalField('table_cards');
	}

	public function getWaitCards() {
		return $this->getAdditionalField('wait_cards');
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
		if ($this['actual_lifes'] < $this['max_lifes']) {
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