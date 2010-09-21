<?php

class Card {
	
	protected static $table = 'card';
	
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
}

?>