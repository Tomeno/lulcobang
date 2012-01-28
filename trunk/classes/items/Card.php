<?php

class Card extends LinkableItem {

	// TODO db model - farba (modra, zelena, hneda), typ - bang, vedla, pivo ..., vacsi typ - zabija, obranuje, zbran, vzdialenost..., ucinok - vsetci hraci,
	// prave jeden hrac, hrac v dosahu, ktorykolvek hrac a dalej zatial neviem

	protected $imageFolder = 'images/cards/bang/playing_cards/';
	protected $back = '../special/playing_card_back.jpg';
	
	const BANG = 1;
	const VEDLA = 2;
	const PIVO = 3;
	const SALOON = 4;
	const HOKYNARSTVI = 7;
	const WELLS_FARGO = 5;
	const DOSTAVNIK = 6;
	const PANIKA = 8;
	const CAT_BALOU = 9;
	const INDIANI = 10;
	const GULOMET = 11;
	const DUEL = 12;
	const MUSTANG = 13;
	const APPALOOSA = 14;
	const BAREL = 15;
	const DYNAMIT = 16;
	const VAZENIE = 17;
	const VOLCANIC = 18;
	const SCHOFIELD = 19;
	const REMINGTON = 20;
	const CARABINA = 21;
	const WINCHESTER = 22;
	
	protected static $guns = array('volcanic', 'schofield', 'remington', 'carabina', 'winchester');

	public function __construct($card) {
		parent::__construct($card);

		$cardBaseTypeRepository = new CardBaseTypeRepository();
		$cardBaseType = $cardBaseTypeRepository->getOneById($this['card_base_type']);

		$this->addAdditionalField('cardBaseType', $cardBaseType);
	}
	
	public function getImagePath() {
		return $this->imageFolder . $this['image'];
	}

	public function getPageType() {
		return 'card';
	}
	
	public function getItemAlias() {
		$cardBaseType = $this->getAdditionalField('cardBaseType');
		if ($cardBaseType) {
			return $cardBaseType['alias'];
		}
	}

	public function getTitle() {
		$cardBaseType = $this->getAdditionalField('cardBaseType');
		if ($cardBaseType) {
			return $cardBaseType->getLocalizedTitle();
		}
	}

	public function getDescription() {
		$cardBaseType = $this->getAdditionalField('cardBaseType');
		if ($cardBaseType) {
			return $cardBaseType->getLocalizedDescription();
		}
	}

	/**
	 * getter for related cards
	 *
	 * @todo doplnit do db typy kariet inym sposobom ako su teraz
	 * @return	array
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

	public function getIsType($type) {
		if ($this['card_base_type'] == $type) {
			return true;
		}
		return false;
	}
	
	public function getIsBang() {
		return $this->getIsType(Card::BANG);
	}
	
	public function getIsVedla() {
		return $this->getIsType(Card::VEDLA);
	}
	
	public function getIsPivo() {
		return $this->getIsType(Card::PIVO);
	}
	
	public function getIsSaloon() {
		return $this->getIsType(Card::SALOON);
	}
	
	public function getIsHokynarstvi() {
		return $this->getIsType(Card::HOKYNARSTVI);
	}
	
	public function getIsWellsFargo() {
		return $this->getIsType(Card::WELLS_FARGO);
	}
	
	public function getIsDostavnik() {
		return $this->getIsType(Card::DOSTAVNIK);
	}
	
	public function getIsPanika() {
		return $this->getIsType(Card::PANIKA);
	}
	
	public function getIsCatbalou() {
		return $this->getIsType(Card::CAT_BALOU);
	}
	
	public function getIsIndiani() {
		return $this->getIsType(Card::INDIANI);
	}
	
	public function getIsGulomet() {
		return $this->getIsType(Card::GULOMET);
	}
	
	public function getIsDuel() {
		return $this->getIsType(Card::DUEL);
	}
	
	public function getIsMustang() {
		return $this->getIsType(Card::MUSTANG);
	}
	
	public function getIsAppaloosa() {
		return $this->getIsType(Card::APPALOOSA);
	}
	
	public function getIsBarel() {
		return $this->getIsType(Card::BAREL);
	}
	
	public function getIsDynamit() {
		return $this->getIsType(Card::DYNAMIT);
	}
	
	public function getIsVazenie() {
		return $this->getIsType(Card::VAZENIE);
	}
	
	public function getIsVolcanic() {
		return $this->getIsType(Card::VOLCANIC);
	}
	
	public function getIsSchofield() {
		return $this->getIsType(Card::SCHOFIELD);
	}
	
	public function getIsRemington() {
		return $this->getIsType(Card::REMINGTON);
	}
	
	public function getIsCarabina() {
		return $this->getIsType(Card::CARABINA);
	}
	
	public function getIsWinchester() {
		return $this->getIsType(Card::WINCHESTER);
	}
	
	public function getIsGun() {
		if ($this->getIsVolcanic() || $this->getIsSchofield() || $this->getIsRemington() || $this->getIsCarabina() || $this->getIsWinchester()) {
			return true;
		}
		return false;
	}
	
	public function getIsDistanceChanger() {
		if ($this->getIsMustang() || $this->getIsAppaloosa()) {
			return true;
		}
		return false;
	}
	
	public function getIsPuttable() {
		if ($this->getIsGun() || $this->getIsDistanceChanger() || $this->getIsVazenie() || $this->getIsBarel() || $this->getIsDynamit()) {
			return true;
		}
		return false;
	}
	
	public static function getGuns() {
		return self::$guns;
	}
}

?>