<?php

class Card extends Item {
	
	protected static $table = 'card';
	
	protected $image;
	
//	public function __construct($card) {
//		$this->image = $card['image'];
//	}
	
	// TODO pouzit card repository
	public static function getCards() {
		$query = 'SELECT * FROM ' . self::$table;
		return $GLOBALS['db']->fetchAll($query);
	}
	
	public static function getCardIds() {
		$cards = self::getCards();
		
		$cardList = array();
		foreach ($cards as $card) {
			$cardList[] = $card['id'];
		}
		return $cardList;
	}
	
	public static function getCard($card) {
		$query = 'SELECT * FROM ' . self::$table . ' WHERE id = ' . $card;
		return $GLOBALS['db']->fetchFirst($query);
	}
}

?>