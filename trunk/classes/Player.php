<?php

class Player extends Item {
	
	public function __construct($player) {
		parent::__construct($player);
		
		$userRepository = new UserRepository();
		$user = $userRepository->getOneById($player['user']);
		$this->offsetSet('user', $user);
		
		$roleRepository = new RoleRepository();
		$role = $roleRepository->getOneById($player['role']);
		$this->offsetSet('role', $role);
		
		$charakterRepository = new CharakterRepository();
		$charakter = $charakterRepository->getOneById($player['charakter']);
		$this->offsetSet('charakter', $charakter);
		
		$cardRepository = new CardRepository();
		
		$handCardsId = unserialize($player['hand_cards']);
		$handCards = array();
		if ($handCardsId) {
			foreach ($handCardsId as $cardId) {
				$handCards[] = $cardRepository->getOneById($cardId);
			}
		}
		$this->offsetSet('hand_cards', $handCards);
		
		$tableCardsId = unserialize($player['table_cards']);
		$tableCards= array();
		if ($tableCardsId) {
			foreach ($tableCardsId as $cardId) {
				$tableCards[] = $cardRepository->getOneById($cardId);
			}
		}
		$this->offsetSet('table_cards', $tableCards);
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
	}
	
	protected function hasCardType($cardType, $place = 'table') {
		$methodName = 'getIs' . $cardType;
		foreach ($this[$place . '_cards'] as $card) {
			if ($card->$methodName()) {
				return $card;
			}
		}
		return false;
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