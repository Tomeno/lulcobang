<?php

class Character {
	
	protected static $table = 'charakter';
	
	public static function getCharacters() {
		$query = 'SELECT * FROM ' . self::$table;
		return $GLOBALS['db']->fetchAll($query);
	}
}

?>