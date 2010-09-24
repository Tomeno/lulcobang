<?php

class Card extends Item {
	
	protected $imageFolder = 'images/cards/bang/playing_cards/';
	
	public function __construct($card) {
		parent::__construct($card);
	}
}

?>