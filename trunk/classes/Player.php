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
	
}

?>