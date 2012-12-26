<?php

/**
 * class for object card
 */
class Card extends LinkableItem {

	// TODO db model - farba (modra, zelena, hneda), typ - bang, vedla, pivo ..., vacsi typ - zabija, obranuje, zbran, vzdialenost..., ucinok - vsetci hraci,
	// prave jeden hrac, hrac v dosahu, ktorykolvek hrac a dalej zatial neviem

	protected $imageFolder = 'images/cards/bang/playing_cards/';
	protected $back = 'images/cards/bang/special/playing_card_back.jpg';

	// group types
	const ATTACKER = 1;
	const DEFENDER = 2;
	const ALCOHOL = 3;
	const DISTANCE_CHANGER = 4;
	const WEAPON = 5;
	const CARD_STOLER = 6;
	const CARD_MAKER = 7;

	// border colors
	const NORMAL = 1;
	const BLUE = 2;
	const GREEN = 3;

	/**
	 * constructor
	 *
	 * @param	array	$card
	 */
	public function __construct($card) {
		parent::__construct($card);

		$cardBaseTypeRepository = new CardBaseTypeRepository(TRUE);
		$cardBaseType = $cardBaseTypeRepository->getOneById($this['card_base_type']);

		$this->setAdditionalField('cardBaseType', $cardBaseType);
	}

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

	/**
	 * getter for page type - part of url
	 *
	 * @return	string
	 */
	public function getPageType() {
		return 'card';
	}

	/**
	 * getter for item alias
	 *
	 * @return	string
	 */
	public function getItemAlias() {
		$cardBaseType = $this->getAdditionalField('cardBaseType');
		if ($cardBaseType) {
			return $cardBaseType['alias'];
		} else {
			throw new Exception('Card ' . $this['title'] . ' has no card_base_type', 1332794787);
		}
	}

	/**
	 * getter for localized title of the card
	 *
	 * @return	string
	 */
	public function getTitle() {
		$cardBaseType = $this->getAdditionalField('cardBaseType');
		if ($cardBaseType) {
			return $cardBaseType->getLocalizedTitle();
		} else {
			throw new Exception('Card ' . $this['title'] . ' has no card_base_type', 1332794788);
		}
	}

	/**
	 * getter for localized description of the card
	 *
	 * @return	string
	 */
	public function getDescription() {
		$cardBaseType = $this->getAdditionalField('cardBaseType');
		if ($cardBaseType) {
			return $cardBaseType->getLocalizedDescription();
		} else {
			throw new Exception('Card ' . $this['title'] . ' has no card_base_type', 1332794789);
		}
	}

	/**
	 * getter for related cards via common card_group_type of their cardBaseType
	 *
	 * @return	array<Card>
	 */
	public function getRelatedCards() {
		$cardBaseType = $this->getAdditionalField('cardBaseType');
		if ($cardBaseType) {
			$cardBaseTypeRepository = new CardBaseTypeRepository();
			$cardBaseTypeRepository->addAdditionalWhere(array('column' => 'id', 'value' => $this['card_base_type'], 'xxx' => '!='));
			$cardBaseTypeList = $cardBaseTypeRepository->getByCardGroupType($cardBaseType['card_group_type']);

			$cardBaseTypes = array();
			foreach ($cardBaseTypeList as $oneCardBaseType) {
				$cardBaseTypes[] = $oneCardBaseType['id'];
			}

			$cardRepository = new CardRepository();
			$cardRepository->setGroupBy('card_base_type');
			return $cardRepository->getByCardBaseType($cardBaseTypes);
		}
	}

	/**
	 * getter for related characters
	 *
	 * @return	array<Character>
	 */
	public function getRelatedCharacters() {
		$characterRelatedCardRepository = new CharacterRelatedCardRepository();
		$characterRelatedCards = $characterRelatedCardRepository->getByCardBaseType($this['card_base_type']);

		$characters = array();
		foreach ($characterRelatedCards as $characterRelatedCard) {
			$characters[] = $characterRelatedCard['charakter'];
		}

		$characterRepository = new CharacterRepository();
		return $characterRepository->getById($characters);
	}

	/**
	 * checks if card is red
	 *
	 * @return	boolean
	 */
	public function getIsRed() {
		if ($this['color'] == 2 || $this['color'] == 4) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function getIsHeart() {
		if ($this['color'] == 2) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function getIsSpades() {
		if ($this['color'] == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * checks if card is type of $type
	 *
	 * @param	integer	$type
	 * @return	boolean
	 */
	protected function getIsType($type) {
		if ($this['card_base_type'] == $type) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * checks if card is type of bang
	 *
	 * @return	boolean
	 */
	public function getIsBang() {
		return $this->getIsType(CardBaseType::BANG);
	}

	/**
	 * checks if card is type of missed
	 *
	 * @return	boolean
	 */
	public function getIsMissed() {
		return $this->getIsType(CardBaseType::MISSED);
	}

	/**
	 * checks if card is type of beer
	 *
	 * @return	boolean
	 */
	public function getIsBeer() {
		return $this->getIsType(CardBaseType::BEER);
	}

	/**
	 * checks if card is type of saloon
	 *
	 * @return	boolean
	 */
	public function getIsSaloon() {
		return $this->getIsType(CardBaseType::SALOON);
	}

	/**
	 * checks if card is type of general store
	 *
	 * @return	boolean
	 */
	public function getIsGeneralstore() {
		return $this->getIsType(CardBaseType::GENERAL_STORE);
	}

	/**
	 * checks if card is type of wells fargo
	 *
	 * @return	boolean
	 */
	public function getIsWellsfargo() {
		return $this->getIsType(CardBaseType::WELLS_FARGO);
	}

	/**
	 * checks if card is type of diligenza
	 *
	 * @return	boolean
	 */
	public function getIsDiligenza() {
		return $this->getIsType(CardBaseType::DILIGENZA);
	}

	/**
	 * checks if card is type of panic
	 *
	 * @return	boolean
	 */
	public function getIsPanic() {
		return $this->getIsType(CardBaseType::PANIC);
	}

	/**
	 * checks if card is type of cat balou
	 *
	 * @return	boolean
	 */
	public function getIsCatbalou() {
		return $this->getIsType(CardBaseType::CAT_BALOU);
	}

	/**
	 * checks if card is type of indians
	 *
	 * @return	boolean
	 */
	public function getIsIndians() {
		return $this->getIsType(CardBaseType::INDIANS);
	}

	/**
	 * checks if card is type of gatling
	 *
	 * @return	boolean
	 */
	public function getIsGatling() {
		return $this->getIsType(CardBaseType::GATLING);
	}

	/**
	 * checks if card is type of duel
	 *
	 * @return	boolean
	 */
	public function getIsDuel() {
		return $this->getIsType(CardBaseType::DUEL);
	}

	/**
	 * checks if card is type of mustang
	 *
	 * @return	boolean
	 */
	public function getIsMustang() {
		return $this->getIsType(CardBaseType::MUSTANG);
	}

	/**
	 * checks if card is type of appaloosa
	 *
	 * @return	boolean
	 */
	public function getIsAppaloosa() {
		return $this->getIsType(CardBaseType::APPALOOSA);
	}

	/**
	 * checks if card is type of barrel
	 *
	 * @return	boolean
	 */
	public function getIsBarrel() {
		return $this->getIsType(CardBaseType::BARREL);
	}

	/**
	 * checks if card is type of dynamite
	 *
	 * @return	boolean
	 */
	public function getIsDynamite() {
		return $this->getIsType(CardBaseType::DYNAMITE);
	}

	/**
	 * checks if card is type of jail
	 *
	 * @return	boolean
	 */
	public function getIsJail() {
		return $this->getIsType(CardBaseType::JAIL);
	}

	/**
	 * checks if card is type of volcanic
	 *
	 * @return	boolean
	 */
	public function getIsVolcanic() {
		return $this->getIsType(CardBaseType::VOLCANIC);
	}

	/**
	 * checks if card is type of schofield
	 *
	 * @return	boolean
	 */
	public function getIsSchofield() {
		return $this->getIsType(CardBaseType::SCHOFIELD);
	}

	/**
	 * checks if card is type of remington
	 *
	 * @return	boolean
	 */
	public function getIsRemington() {
		return $this->getIsType(CardBaseType::REMINGTON);
	}

	/**
	 * checks if card is type of rev. carabine
	 *
	 * @return	boolean
	 */
	public function getIsRevcarabine() {
		return $this->getIsType(CardBaseType::REV_CARABINE);
	}

	/**
	 * checks if card is type of winchester
	 *
	 * @return	boolean
	 */
	public function getIsWinchester() {
		return $this->getIsType(CardBaseType::WINCHESTER);
	}

	/**
	 * checks if card is type of dodge
	 *
	 * @return	boolean
	 */
	public function getIsDodge() {
		return $this->getIsType(CardBaseType::DODGE);
	}

	/**
	 * checks if card is type of punch
	 *
	 * @return	boolean
	 */
	public function getIsPunch() {
		return $this->getIsType(CardBaseType::PUNCH);
	}

	/**
	 * checks if card is type of springfield
	 *
	 * @return	boolean
	 */
	public function getIsSpringfield() {
		return $this->getIsType(CardBaseType::SPRINGFIELD);
	}

	/**
	 * checks if card is type of brawl
	 *
	 * @return	boolean
	 */
	public function getIsBrawl() {
		return $this->getIsType(CardBaseType::BRAWL);
	}

	/**
	 * checks if card is type of rag time
	 *
	 * @return	boolean
	 */
	public function getIsRagtime() {
		return $this->getIsType(CardBaseType::RAG_TIME);
	}

	/**
	 * checks if card is type of tequila
	 *
	 * @return	boolean
	 */
	public function getIsTequila() {
		return $this->getIsType(CardBaseType::TEQUILA);
	}

	/**
	 * checks if card is type of hideout
	 *
	 * @return	boolean
	 */
	public function getIsHideout() {
		return $this->getIsType(CardBaseType::HIDEOUT);
	}

	/**
	 * checks if card is type of silver
	 *
	 * @return	boolean
	 */
	public function getIsSilver() {
		return $this->getIsType(CardBaseType::SILVER);
	}

	/**
	 * checks if card is type of sombrero
	 *
	 * @return	boolean
	 */
	public function getIsSombrero() {
		return $this->getIsType(CardBaseType::SOMBRERO);
	}

	/**
	 * checks if card is type of iron plate
	 *
	 * @return	boolean
	 */
	public function getIsIronplate() {
		return $this->getIsType(CardBaseType::IRON_PLATE);
	}

	/**
	 * checks if card is type of ten gallon hat
	 *
	 * @return	boolean
	 */
	public function getIsTengallonhat() {
		return $this->getIsType(CardBaseType::TEN_GALLON_HAT);
	}

	/**
	 * checks if card is type of bible
	 *
	 * @return	boolean
	 */
	public function getIsBible() {
		return $this->getIsType(CardBaseType::BIBLE);
	}

	/**
	 * checks if card is type of canteen
	 *
	 * @return	boolean
	 */
	public function getIsCanteen() {
		return $this->getIsType(CardBaseType::CANTEEN);
	}

	/**
	 * checks if card is type of knife
	 *
	 * @return	boolean
	 */
	public function getIsKnife() {
		return $this->getIsType(CardBaseType::KNIFE);
	}

	/**
	 * checks if card is type of derringer
	 *
	 * @return	boolean
	 */
	public function getIsDerringer() {
		return $this->getIsType(CardBaseType::DERRINGER);
	}

	/**
	 * checks if card is type of howitzer
	 *
	 * @return	boolean
	 */
	public function getIsHowitzer() {
		return $this->getIsType(CardBaseType::HOWITZER);
	}

	/**
	 * checks if card is type of pepperbox
	 *
	 * @return	boolean
	 */
	public function getIsPepperbox() {
		return $this->getIsType(CardBaseType::PEPPERBOX);
	}

	/**
	 * checks if card is type of buffalo rifle
	 *
	 * @return	boolean
	 */
	public function getIsBuffalorifle() {
		return $this->getIsType(CardBaseType::BUFFALO_RIFLE);
	}

	/**
	 * checks if card is type of can can
	 *
	 * @return	boolean
	 */
	public function getIsCancan() {
		return $this->getIsType(CardBaseType::CAN_CAN);
	}

	/**
	 * checks if card is type of conestoga
	 *
	 * @return	boolean
	 */
	public function getIsConestoga() {
		return $this->getIsType(CardBaseType::CONESTOGA);
	}

	/**
	 * checks if card is type of pony express
	 *
	 * @return	boolean
	 */
	public function getIsPonyexpress() {
		return $this->getIsType(CardBaseType::PONY_EXPRESS);
	}

	/**
	 * checks if card is type of whisky
	 *
	 * @return	boolean
	 */
	public function getIsWhisky() {
		return $this->getIsType(CardBaseType::WHISKY);
	}

	/**
	 * getter for card group type
	 *
	 * @return	integer
	 */
	protected function getCardGroupType() {
		$cardBaseType = $this->getAdditionalField('cardBaseType');
		return $cardBaseType['card_group_type'];
	}

	/**
	 * getter for card border color
	 *
	 * @return	integer
	 */
	protected function getCardBorderColor() {
		$cardBaseType = $this->getAdditionalField('cardBaseType');
		return $cardBaseType['card_border_color'];
	}

	/**
	 * checks if card is weapon
	 *
	 * @return	boolean
	 */
	public function getIsWeapon() {
		if ($this->getCardGroupType() == self::WEAPON) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * checks if card changes distance
	 *
	 * @return	boolean
	 */
	public function getIsDistanceChanger() {
		if ($this->getCardGroupType() == self::DISTANCE_CHANGER) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * checks if card is blue
	 *
	 * @return	boolean
	 */
	public function getIsBlue() {
		if ($this->getCardBorderColor() == self::BLUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * checks if card is green
	 *
	 * @return	boolean
	 */
	public function getIsGreen() {
		if ($this->getCardBorderColor() == self::GREEN) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * checks if player can put card to the table
	 *
	 * @return	boolean
	 */
	public function getIsPuttable() {
		if ($this->getIsBlue() || $this->getIsGreen()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

?>