<?php

class CardListingBox extends AbstractBox {
	
	protected $template = 'card-listing.tpl';
	
	protected function setup() {
		$cardRepository = new CardRepository();
		$cards = $cardRepository->getAll();

		MySmarty::assign('cards', $cards);
	}
}

?>