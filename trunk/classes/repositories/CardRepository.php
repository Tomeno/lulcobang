<?php

class CardRepository extends Repository {
	
	protected $table = 'card';
	
	public function getCardIds() {
		$cards = $this->getAll();
		
		$cardList = array();
		foreach ($cards as $card) {
			$cardList[] = $card['id'];
		}
		return $cardList;
	}
}

?>