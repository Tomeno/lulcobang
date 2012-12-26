<?php

class IndexBox extends AbstractBox {
	protected $template = 'homepage.tpl';
	
	protected function setup() {
		// cards
		$cardBaseTypeRepository = new CardBaseTypeRepository();
		$validCardBaseTypes = $cardBaseTypeRepository->getByValid(1);

		$validCardBaseTypesIdList = array();
		foreach ($validCardBaseTypes as $cardBaseType) {
			$validCardBaseTypesIdList[] = $cardBaseType['id'];
		}

		$cardRepository = new CardRepository();
		$cardRepository->setGroupBy('card_base_type');
		$cards = $cardRepository->getAll();
	
		$validCards = array();
		$notValidCards = array();
		foreach ($cards as $card) {
			if (in_array($card['card_base_type'], $validCardBaseTypesIdList)) {
				$validCards[] = $card;
			} else {
				$notValidCards[] = $card;
			}
		}
		
		MySmarty::assign('validCards', $validCards);
		MySmarty::assign('notValidCards', $notValidCards);
		
		// characters
		$characterRepository = new CharacterRepository();
		$characters = $characterRepository->getAll();
		
		$validCharacters = array();
		$notValidCharacters = array();
		foreach ($characters as $character) {
			if ($character['valid'] == 1) {
				$validCharacters[] = $character;
			} else {
				$notValidCharacters[] = $character;
			}
		}
		MySmarty::assign('validCharacters', $validCharacters);
		MySmarty::assign('notValidCharacters', $notValidCharacters);
	}
}

?>