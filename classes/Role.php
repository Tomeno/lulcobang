<?php

class Role {
	
	protected static $table = 'role';
	
	public static function getRoles($playerCount) {
		$query = 'SELECT * FROM ' . self::$table . ' LIMIT ' . $playerCount;
		return $GLOBALS['db']->fetchAll($query);
	}
}

?>