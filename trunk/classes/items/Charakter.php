<?php

class Charakter extends Item {
	
	protected $imageFolder = 'images/cards/bang/characters/';
	protected $back = '../special/character_back.jpg';
	
	public function __construct($charakter) {
		parent::__construct($charakter);
	}
}

?>