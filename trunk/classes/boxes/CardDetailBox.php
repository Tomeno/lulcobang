<?php

class CardDetailBox extends AbstractBox {

	protected $template = 'card-detail.tpl';

	protected function setup() {
		$cardId = intval(str_replace('karta-', '', Utils::get('identifier')));
		$cardRepository = new CardRepository();
		$card = $cardRepository->getOneById($cardId);

		MySmarty::assign('card', $card);
	}
}

?>