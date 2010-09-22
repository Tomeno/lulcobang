<?php

class Charakter extends Item {
	
	protected $imageFolder = 'images/cards/bang/characters/';
	
	public function __construct($charakter) {
		parent::__construct($charakter);
	}
	
}

?>