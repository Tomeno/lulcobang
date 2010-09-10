<?php

class Room {
	
	protected static $table = 'room';
	
	public static function getRooms() {
		$query = 'SELECT * FROM ' . self::$table;
		return $GLOBALS['db']->fetchAll($query);
	}
	
	public static function addRoom($title, $description) {
		$params = array(
			'title' => $title,
			'description' => $description,
		);
		return $GLOBALS['db']->insert(self::$table, $params);
	}
}

?>