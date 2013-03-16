<?php

class HighNoon extends Item {

	protected $highNoonImageFolder = 'images/cards/bang/extensions/high_noon/';
	protected $highNoonBack = 'images/cards/bang/special/high_noon_back.jpg';

	protected $fistfulOfCardsImageFolder = 'images/cards/bang/extensions/a_fistful_of_cards/';
	protected $fistfulOfCardsNoonBack = 'images/cards/bang/special/fistful_back.jpg';
	
	protected $wildWestShowImageFolder = 'images/cards/bang/extensions/wild_west_show/';
	protected $wildWestShowBack = 'images/cards/bang/special/wild_west_show.jpg';


	const CARD_HIGH_NOON = 6;
	const CARD_FISTFUL_OF_CARDS = 28;
	const CARD_WILD_WEST_SHOW = 38;
	
	/**
	 * getter for card image path
	 *
	 * @return	string
	 */
	public function getImagePath() {
		$imageFolder = '';
		if ($this['game_set'] == Game::GAME_SET_HIGH_NOON) {
			$imageFolder = $this->highNoonImageFolder;
		} elseif ($this['game_set'] == Game::GAME_SET_A_FISTFUL_OF_CARDS) {
			$imageFolder = $this->fistfulOfCardsImageFolder;
		} elseif ($this['game_set'] == Game::GAME_SET_WILD_WEST_SHOW) {
			$imageFolder = $this->wildWestShowImageFolder;
		}
		return $imageFolder . $this['image'];
	}

	/**
	 * getter for back card image
	 *
	 * @return	string
	 */
	public function getBackImagePath() {
		if ($this['game_set'] == Game::GAME_SET_HIGH_NOON) {
			return $this->highNoonBack;
		} elseif ($this['game_set'] == Game::GAME_SET_A_FISTFUL_OF_CARDS) {
			return $this->fistfulOfCardsNoonBack;
		} elseif ($this['game_set'] == Game::GAME_SET_WILD_WEST_SHOW) {
			return $this->wildWestShowBack;
		}
	}
	
	public function getLocalizedTitle() {
		return Localize::getMessage($this['localize_key'] . '_title');
	}
	
	public function getLocalizedDescription() {
		return Localize::getMessage($this['localize_key'] . '_description');
	}
	
	public static function getSpecialCards() {
		return array(
			self::CARD_HIGH_NOON,
			self::CARD_FISTFUL_OF_CARDS,
			self::CARD_WILD_WEST_SHOW,
		);
	}
}

?>