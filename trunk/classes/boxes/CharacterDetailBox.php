<?php

class CharacterDetailBox extends AbstractBox {

	protected $template = 'character-detail.tpl';

	protected function setup() {
		$characterAlias = Utils::get('identifier');

		$characterRepository = new CharacterRepository();
		$character = $characterRepository->getOneByAlias($characterAlias);

		BangSeo::addTitlePart($character['name']);
		if ($character->getLocalizedDescription()) {
			BangSeo::setDescription($character->getLocalizedDescription());
		}

		MySmarty::assign('character', $character);
	}
}

?>