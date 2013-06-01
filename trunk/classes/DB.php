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
		self::query('SET names UTF8');
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
		// Log::logQuery($query);
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
	public static function fetchAll($query, $repository = NULL, $useCache = FALSE) {
		$result = self::getFromCache($query, $useCache);
		
		if ($result !== NULL && $result !== FALSE) {
			return $result;
		}

		$res = self::query($query);
		$result = array();
		while ($row = mysql_fetch_assoc($res)) {
			$class = $repository ? str_replace('Repository', '', $repository) : '';
			$item = $class ? new $class($row) : $row;
			
			if (isset($row['id'])) {
				// dame result do pola podla ideciek
				$result[$row['id']] = $item;
			} else {
				$result[] = $item;
			}
		}
		
		self::saveToCache($query, $result, $useCache);
		
		return $result;
	}

	/**
	 * fetch first Item by query
	 *
	 * @param	string	$query
	 * @param	string	$repository
	 * @return	Item|NULL
	 */
	public static function fetchFirst($query, $repository = NULL, $useCache = FALSE) {
		$foundString = 'limit 1';
		$queryClon = mb_strtolower($query);

		if ((strpos('limit', $queryClon) !== FALSE) && (strlen($queryClon) != (strlen($foundString) + strpos($queryClon, $foundString)))) {
			$query .= ' LIMIT 1';
		}
		$rows = self::fetchAll($query, $repository, $useCache);
		if (count($rows)) {
			return current($rows);
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
	
	public static function getFromCache($query, $useCache) {
		$result = NULL;
		if ($useCache === TRUE && defined('QUERY_CACHE_CLASS')) {
			$queryCacheClass = QUERY_CACHE_CLASS;
			$queryCacheInstance = $queryCacheClass::instance();
			
			$cacheKey = self::getCacheKey($query);
			$result = $queryCacheInstance->get($cacheKey);
		}
		return $result;
	}
	
	public static function saveToCache($query, $data, $useCache) {
		if ($useCache === TRUE && defined('QUERY_CACHE_CLASS')) {
			$queryCacheClass = QUERY_CACHE_CLASS;
			$queryCacheInstance = $queryCacheClass::instance();
			
			$cacheKey = self::getCacheKey($query);
			$queryCacheInstance->set($cacheKey, $data, NULL, '+2 hours');
		}
	}
	
	public static function getCacheKey($query) {
		return 'query_' . md5($query);
	}
}

?>