<?php

class Charakter extends Item {
	
	protected $imageFolder = 'images/cards/bang/characters/';
	protected $back = 'back.jpg';
	
	public function __construct($charakter) {
		parent::__construct($charakter);
	}
}

?>