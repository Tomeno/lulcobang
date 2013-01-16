<?php

class HighNoon extends Item {

	protected $imageFolder = 'images/cards/bang/extensions/high_noon/';
	protected $back = 'images/cards/bang/special/high_noon_back.jpg';

	const CARD_HIGH_NOON = 6;
	
	/**
	 * getter for card image path
	 *
	 * @return	string
	 */
	public function getImagePath() {
		return $this->imageFolder . $this['image'];
	}

	/**
	 * getter for back card image
	 *
	 * @return	string
	 */
	public function getBackImagePath() {
		return $this->back;
	}
}

?>