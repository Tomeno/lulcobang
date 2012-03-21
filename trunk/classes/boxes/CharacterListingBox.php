<?php

/**
 * Box for character listing
 *
 * @author Michal Lulco <michal.lulco@gmail.com>
 */
class CharacterListingBox extends AbstractBox {

	protected $template = 'character-listing.tpl';

	protected function setup() {
		$characterRepository = new CharacterRepository();
		$characterRepository->addOrderBy(array('name' => 'ASC'));
		$characters = $characterRepository->getAll();

		MySmarty::assign('characters', $characters);
	}
}

?>