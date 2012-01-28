<?php

class CharactersAction extends AbstractAction {

	public function getContent() {
		if (Utils::get('identifier')) {
			$page = PageActionMap::getPageByTypeAndLanguage('character');
			if ($page['alias'] != Utils::get('action')) {
				$url = PageActionMap::createUrl(array($page['alias'], Utils::get('identifier')), Utils::get('language'));
				Utils::redirect($url);
			}
			$box = new CharacterDetailBox();
		} else {
			$page = PageActionMap::getPageByTypeAndLanguage('characters');
			if ($page['alias'] != Utils::get('action')) {
				$url = PageActionMap::createUrl(array($page['alias']), Utils::get('language'));
				Utils::redirect($url);
			}
			$box = new CharacterListingBox();
		}
		return $box->render();
	}
}

?>