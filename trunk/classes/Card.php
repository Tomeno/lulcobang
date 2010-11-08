<?php

class Card extends Item {
	
	protected $imageFolder = 'images/cards/bang/playing_cards/';
	protected $back = 'back.jpg';
	
	const BANG = 1;
	const VEDLA = 2;
	const PIVO = 3;
	const SALOON = 4;
	const HOKYNARSTVI = 5;
	const WELLS_FARGO = 6;
	const DOSTAVNIK = 7;
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
	}
	
	public function getIsType($type) {
		if ($this['card_type'] == $type) {
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