<?php

/**
 * db class
 */
class DB {
	
	protected static $link = NULL;
	
	protected static $dbName = NULL;

	/**
	 * private constructor
	 *
	 * @param	string	$username
	 * @param	string	$password
	 * @param	string	$server
	 * @param	string	$database
	 */
	private function __construct($username, $password, $server, $database) {
		self::$link = mysql_connect($server, $username, $password, TRUE) or die("Cannot connect to '$server'\n");
		mysql_select_db($database, self::$link);
		self::$dbName = $database;
		mysql_query('SET names UTF8', self::$link);
	}

	/**
	 * initialize
	 *
	 * @return	void
	 */
	private static function init() {
		if (self::$link === NULL) {
			new DB(DB_USERNAME, DB_PASSWORD, DB_HOST, DB_NAME);
		}
	}

	/**
	 * query
	 *
	 * @param	string	$query
	 * @return	resource
	 */
	protected static function query($query) {
		self::init();
		$res = mysql_query($query, self::$link);
		if (mysql_error(self::$link)) {
			echo $query;
			throw new Exception('MYSQL ERROR: ' . mysql_errno(self::$link) .': ' . mysql_error(self::$link));
		}
		return $res;
	}

	/**
	 * fetch all items by query
	 *
	 * @param	string	$query
	 * @param	string	$repository
	 * @return	array<Item>
	 */
	public static function fetchAll($query, $repository = NULL) {
		$res = self::query($query);
		$result = array();
		while ($row = mysql_fetch_assoc($res)) {
			$class = $repository ? str_replace('Repository', '', $repository) : '';
			$result[] = $class ? new $class($row) : $row;
		}
		return $result;
	}

	/**
	 * fetch first Item by query
	 *
	 * @param	string	$query
	 * @param	string	$repository
	 * @return	Item|NULL
	 */
	public static function fetchFirst($query, $repository = NULL) {
		$foundString = 'limit 1';
		$queryClon = mb_strtolower($query);

		if ((strpos('limit', $queryClon) !== FALSE) && (strlen($queryClon) != (strlen($foundString) + strpos($queryClon, $foundString)))) {
			$query .= ' LIMIT 1';
		}

		$rows = self::fetchAll($query, $repository);
		if (count($rows)) {
			return $rows[0];
		}
		return NULL;
	}

	/**
	 * insert $data to $table
	 *
	 * @param	string	$table
	 * @param	array	$data
	 * @return	int	inserted id
	 */
	public static function insert($table, $data) {
		$params = array();
		foreach ($data as $key => $value) {
			$value = "'".addslashes($value)."'";
			$params[$key] = $value;
		}
		$query = "INSERT INTO $table (".implode(',', array_keys($params)).") VALUES (".implode(',', array_values($params)).")";
		self::query($query);
		$id = mysql_insert_id(self::$link);
		return $id;
	}

	/**
	 * updates $table by $params where $where
	 *
	 * @param	string	$table
	 * @param	array	$params
	 * @param	string	$where
	 * @return	resource
	 */
	public static function update($table, $data, $where = '') {
		$parts = array();
		foreach ($data as $key => $value) {
			$parts[] = "$key='" . addslashes($value) . "'";
		}
		$set = implode(', ', $parts);
		$query = 'UPDATE ' . $table . ' SET ' . $set . ($where ? ' WHERE ' . $where : '');
		return self::query($query);
	}

	/**
	 * delete from $table where $where
	 *
	 * @param	string	$table
	 * @param	string	$where
	 * @return	resource
	 */
	public static function delete($table, $where = '') {
		$query = 'DELETE FROM ' . $table . ($where ? ' WHERE ' . $where : '');
		return self::query($query);
	}
}

?>