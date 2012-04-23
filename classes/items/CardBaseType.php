<?php

class CardBaseType extends Item {

	// types
	const BANG = 1;
	const MISSED = 2;
	const BEER = 3;
	const SALOON = 4;
	const WELLS_FARGO = 5;
	const DILIGENZA = 6;
	const GENERAL_STORE = 7;
	const PANIC = 8;
	const CAT_BALOU = 9;
	const INDIANS = 10;
	const DUEL = 11;
	const GATLING = 12;
	const MUSTANG = 13;
	const APPALOOSA = 14;
	const BARREL = 15;
	const DYNAMITE = 16;
	const JAIL = 17;
	const VOLCANIC = 18;
	const SCHOFIELD = 19;
	const REMINGTON = 20;
	const REV_CARABINE = 21;
	const WINCHESTER = 22;
	const DODGE = 23;
	const PUNCH = 24;
	const SPRINGFIELD = 25;
	const BRAWL = 26;
	const RAG_TIME = 27;
	const TEQUILA = 28;
	const HIDEOUT = 29;
	const SILVER = 30;
	const SOMBRERO = 31;
	const IRON_PLATE = 32;
	const TEN_GALLON_HAT = 33;
	const BIBLE = 34;
	const CANTEEN = 35;
	const KNIFE = 36;
	const DERRINGER = 37;
	const HOWITZER = 38;
	const PEPPERBOX = 39;
	const BUFFALO_RIFLE = 40;
	const CAN_CAN = 41;
	const CONESTOGA = 42;
	const PONY_EXPRESS = 43;
	const WHISKY = 44;

	public function getLocalizedTitle() {
		return Localize::getMessage($this['localize_title_key']);
	}

	public function getLocalizedDescription() {
		return Localize::getMessage($this['localize_description_key']);
	}
}

?>