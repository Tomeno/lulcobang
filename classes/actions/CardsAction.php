<?php

class CardsAction extends AbstractAction {

	public function getContent() {
		if (Utils::get('identifier')) {
			$page = PageActionMap::getPageByTypeAndLanguage('card');
			if ($page['alias'] != Utils::get('action')) {
				$url = PageActionMap::createUrl(array($page['alias'], Utils::get('identifier')), Utils::get('language'));
				Utils::redirect($url);
			}
			$box = new CardDetailBox();
		} else {
			$page = PageActionMap::getPageByTypeAndLanguage('cards');
			if ($page['alias'] != Utils::get('action')) {
				$url = PageActionMap::createUrl(array($page['alias']), Utils::get('language'));
				Utils::redirect($url);
			}
			$box = new CardListingBox();
		}
		return $box->render();
	}
}

?>