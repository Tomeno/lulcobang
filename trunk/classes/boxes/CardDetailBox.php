<?php

class CardDetailBox extends AbstractBox {

	protected $template = 'card-detail.tpl';

	protected function setup() {
		$cardAlias = Utils::get('identifier');
		$cardBaseTypeRepository = new CardBaseTypeRepository();
		$cardBaseType = $cardBaseTypeRepository->getOneByAlias($cardAlias);

		if ($cardBaseType) {
			$cardRepository = new CardRepository();
			$card = $cardRepository->getOneByCardBaseType($cardBaseType['id']);
		}

		BangSeo::addTitlePart($card->getTitle());
		if ($card->getDescription()) {
			BangSeo::setDescription($card->getDescription());
		}
		
		MySmarty::assign('card', $card);
	}
}

?>