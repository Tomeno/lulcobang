<?php

class Player extends LinkableItem {

	const PHASE_NONE = 0;
	const PHASE_PREDRAW = 1;
	const PHASE_DYNAMITE = 2;
	const PHASE_JAIL = 3;
	const PHASE_DRAW = 4;
	const PHASE_PLAY = 5;
	const PHASE_UNDER_ATTACK = 6;
	const PHASE_WAITING = 7;
	
	public function __construct($player) {
		parent::__construct($player);
		
		$userRepository = new UserRepository();
		$user = $userRepository->getOneById($player['user']);
		$this->setAdditionalField('user', $user);
		
		$roleRepository = new RoleRepository(TRUE);
		$role = $roleRepository->getOneById($player['role']);
		$this->setAdditionalField('role', $role);
		
		$characterRepository = new CharacterRepository(TRUE);
		$character = $characterRepository->getOneById($player['charakter']);
		$this->setAdditionalField('character', $character);
		
		$cardRepository = new CardRepository(TRUE);
		
		$handCardsId = unserialize($player['hand_cards']);
		$handCards = array();
		if ($handCardsId) {
			$cardRepository->addOrderBy(array('card_base_type' => 'ASC'));
			$handCards = $cardRepository->getById($handCardsId);
		}
		$this->setAdditionalField('hand_cards', $handCards);
		
		$cardRepository = new CardRepository(TRUE);

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
		
		if ($player['notices']) {
			$notices = unserialize($player['notices']);
		} else {
			$notices = array();
		}
		$this->setAdditionalField('notice_list', $notices);
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
	
	protected function getPageType() {
		return 'user';
	}

	protected function getItemAlias() {
		$user = $this->getUser();
		if ($user) {
			return $user['username'];
		}
		return '';
	}

	/**
	 * checks if player has card type
	 *
	 * @param string $cardType
	 * @param string $place
	 * @return Card if has card | NULL if has not | FALSE if method doesn't exist
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

//	public function setPhase($phase) {
//		$GLOBALS['db']->update('player', array('phase' => $phase), 'id = ' . intval($this['id']));
//	}
	
	/**
	 * checks if player can pass
	 *
	 * @todo special ability for character ...
	 * 
	 * @return boolean
	 */
//	public function getCanPass() {
//		if ($this['actual_lifes'] >= count($this->getAdditionalField('hand_cards'))) {
//			return true;
//		}
//		else {
//			return false;
//		}
//	}
	
	public function getRange() {
		$card = $this->getHasGun();
		if ($card) {
			if ($card->getIsSchofield()) {
				return 2;
			}
			elseif ($card->getIsRemington()) {
				return 3;
			}
			elseif ($card->getIsRevcarabine()) {
				return 4;
			}
			elseif ($card->getIsWinchester()) {
				return 5;
			}
		}
		return 1;
	}
	
	public function getHasGun() {
		$tableCards = $this->getTableCards();
		foreach ($tableCards as $tableCard) {
			if ($tableCard->getIsWeapon()) {
				return $tableCard;
			}
		}
		return FALSE;
	}

	public function getNoticeList() {
		return $this->getAdditionalField('notice_list');
	}
	
	public function setNoticeList($noticeList) {
		$this->setAdditionalField('notice_list', $noticeList);
		$this['notices'] = serialize($noticeList);
	}
}

?>