<?php

class CharacterDetailBox extends AbstractBox {

	protected $template = 'character-detail.tpl';

	protected function setup() {
		$characterAlias = Utils::get('identifier');

		$characterRepository = new CharacterRepository();
		$character = $characterRepository->getOneByAlias($characterAlias);

		MySmarty::assign('character', $character);
	}
}

?>