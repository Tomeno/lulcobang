<?php

class CardsAction extends AbstractAction {

	public function getContent() {
		if (Utils::get('identifier')) {
			$box = new CardDetailBox();
		} else {
			$box = new CardListingBox();
		}
		return $box->render();
	}
}

?>