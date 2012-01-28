<?php

class Character extends LinkableItem {
	
	protected $imageFolder = 'images/cards/bang/characters/';
	protected $back = '../special/character_back.jpg';
	
	public function __construct($character) {
		parent::__construct($character);
	}

	public function getImagePath() {
		return $this->imageFolder . $this['image'];
	}

	public function getPageType() {
		return 'character';
	}

	public function getItemAlias() {
		return $this['alias'];
	}

	public function getLocalizedDescription() {
		return Localize::getMessage($this['localize_description_key']);
	}

	public function getRelatedCards() {
		$characterRelatedCardRepository = new CharacterRelatedCardRepository();
		$characterRelatedCards = $characterRelatedCardRepository->getByCharakter($this['id']);

		$cardBaseTypes = array();
		foreach ($characterRelatedCards as $characterRelatedCard) {
			$cardBaseTypes[] = $characterRelatedCard['card_base_type'];
		}

		$cardRepository = new CardRepository();
		$cardRepository->setGroupBy('card_base_type');
		return $cardRepository->getByCardBaseType($cardBaseTypes);
	}
}

?>