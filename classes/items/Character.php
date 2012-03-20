<?php

class Character extends LinkableItem {

	const BART_CASSIDY = 1;
	const BLACK_JACK = 2;
	const CALAMITY_JANET = 3;
	const EL_GRINGO = 4;
	const JESSE_JONES = 5;
	const JOURDONNAIS = 6;
	const KIT_CARLSON = 7;
	const LUCKY_DUKE = 8;
	const PAUL_REGRET = 9;
	const PEDRO_RAMIREZ = 10;
	const ROSE_DOOLAN = 11;
	const SID_KETCHUM = 12;
	const SLAB_THE_KILLER = 13;
	const SUZY_LAFAYETTE = 14;
	const VULTURE_SAM = 15;
	const WILLY_THE_KID = 16;
	const APACHE_KID = 17;
	const BELLE_STAR = 18;
	const BILL_NOFACE = 19;
	const CHUCL_WENGAM = 20;
	const DOC_HOLYDAY = 21;
	const ELENA_FUENTE = 22;
	const GREG_DIGGER = 23;
	const HERB_HUNTER = 24;
	const JOSE_DELGADO = 25;
	const MOLLY_STARK = 26;
	const PAT_BRENNAN = 27;
	const PIXIE_PETE = 28;
	const SEAN_MALLORY = 29;
	const TEQUILA_JOE = 30;
	const VERA_CUSTER = 31;

	protected $imageFolder = 'images/cards/bang/characters/';
	protected $back = '../special/character_back.jpg';
	
	public function __construct($character) {
		parent::__construct($character);
	}

	public function  __call($methodName, $arguments) {
		if (substr($methodName, 0, 5) === 'getIs') {
			$character = strtolower(str_replace('getIs', '', $methodName));
			$realCharacter = strtolower(str_replace(array(' ', '\''), '', $this['name']));
			
			if ($character == $realCharacter) {
				return TRUE;
			}
			return FALSE;
		}
	}

	public function getImagePath() {
		return $this->imageFolder . $this['image'];
	}

	public function getPageType() {
		return 'character';
	}

	public function getItemAlias() {
		return $this['alias'];
	}

	public function getLocalizedDescription() {
		return Localize::getMessage($this['localize_description_key']);
	}

	public function getRelatedCards() {
		$characterRelatedCardRepository = new CharacterRelatedCardRepository();
		$characterRelatedCards = $characterRelatedCardRepository->getByCharakter($this['id']);

		$cardBaseTypes = array();
		foreach ($characterRelatedCards as $characterRelatedCard) {
			$cardBaseTypes[] = $characterRelatedCard['card_base_type'];
		}

		$cardRepository = new CardRepository();
		$cardRepository->setGroupBy('card_base_type');
		return $cardRepository->getByCardBaseType($cardBaseTypes);
	}
}

?>