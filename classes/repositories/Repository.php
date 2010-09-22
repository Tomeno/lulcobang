<?php

/**
 * abstract repository
 */
abstract class Repository {
	
	protected $table = '';

	/**
	 * magic method to decide which method to use
	 *
	 * @param string $methodName
	 * @param mixed $value
	 * @return array<Item> | Item
	 * @throws Exception
	 */
	public function __call($methodName, $value) {
		$value = $value[0];
		if (substr($methodName, 0, 5) === 'getBy') {
			$key = strtolower(str_replace('getBy', '', $methodName));
			return $this->getBy($key, $value);
		}
		elseif (substr($methodName, 0, 8) == 'getOneBy') {
			$key = strtolower(str_replace('getOneBy', '', $methodName));
			return $this->getOneBy($key, $value);
		}
		throw new Exception('method ' . get_class($this) . '::' . $methodName . ' doesn\'t exist');
	}
	
	/**
	 * get one item by key and value
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return Item
	 */
	protected function getOneBy($key, $value) {
		$query = 'SELECT * FROM ' . $this->table . ' WHERE ' . $key . ' = ' . $value;
		return $GLOBALS['db']->fetchFirst($query, get_class($this));
	}
	
	/**
	 * get all items by key and value
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return array<Item>
	 */
	protected function getBy($key, $value) {
		$query = 'SELECT * FROM ' . $this->table . ' WHERE ' . $key . ' = ' . $value;
		return $GLOBALS['db']->fetchAll($query, get_class($this));
	}
	
	/**
	 * get all items from table
	 *
	 * @return array<Item>
	 */
	public function getAll() {
		$query = 'SELECT * FROM ' . $this->table;
		return $GLOBALS['db']->fetchAll($query, get_class($this));
	}
}

?>