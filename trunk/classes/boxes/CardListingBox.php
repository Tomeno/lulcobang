<?php

class CardListingBox extends AbstractBox {
	
	protected $template = 'card-listing.tpl';
	
	protected function setup() {
		$cardRepository = new CardRepository();
		$cardRepository->setGroupBy('card_base_type');
		$cards = $cardRepository->getAll();

		MySmarty::assign('cards', $cards);
	}
}

?>