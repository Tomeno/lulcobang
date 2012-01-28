<?php

class CharacterListingBox extends AbstractBox {

	protected $template = 'character-listing.tpl';

	protected function setup() {
		$characterRepository = new CharacterRepository();
		$characters = $characterRepository->getAll();

		MySmarty::assign('characters', $characters);
	}
}

?>