<?php

/**
 * abstract repository
 *
 * TODO getCount query
 */
abstract class Repository {
	
	protected $table = '';

	protected $orderBy = array();

	protected $groupBy = array();

	protected $limit = 0;

	protected $additionalWhere = array();

	/**
	 * getter for table
	 *
	 * @return	string
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * magic method to decide which method to use
	 *
	 * @param	string	$methodName
	 * @param	array	$values
	 * @return	array<Item> | Item
	 * @throws	Exception
	 */
	public function __call($methodName, $values) {
		if (substr($methodName, 0, 5) === 'getBy') {
			$keyString = str_replace('getBy', '', $methodName);
			return $this->get($keyString, $values);
		} elseif (substr($methodName, 0, 8) == 'getOneBy') {
			$keyString = str_replace('getOneBy', '', $methodName);
			return $this->getOne($keyString, $values);
		} elseif (substr($methodName, 0, 10) == 'getCountBy') {
			$keyString = str_replace('getCountBy', '', $methodName);
			return $this->getCountBy($keyString, $values);
		}
		throw new Exception('method ' . get_class($this) . '::' . $methodName . ' doesn\'t exist');
	}
	
	/**
	 * get one item by keys and values
	 *
	 * @param	string	$keyString
	 * @param	array	$values
	 * @return	Item
	 */
	protected function getOne($keyString, $values) {
		$this->setLimit(1);
		$query = $this->getQuery($keyString, $values);
		return DB::fetchFirst($query, get_class($this));
	}
	
	/**
	 * get all items by keys and values
	 *
	 * @param	string	$keyString
	 * @param	array	$values
	 * @return	array<Item>
	 */
	protected function get($keyString, $values) {
		$query = $this->getQuery($keyString, $values);
		return DB::fetchAll($query, get_class($this));
	}

	/**
	 * get all items from table
	 *
	 * @return	array<Item>
	 */
	public function getAll() {
		$query = 'SELECT * FROM ' . $this->table . $this->getAdditionalWhere(TRUE) . $this->getGroupBy() . $this->getOrderBy() . $this->getLimit();
		return DB::fetchAll($query, get_class($this));
	}
	
	/**
	 * get count of items with condition
	 *
	 * @param	string	$keyString
	 * @param	array	$values
	 * @return	int
	 */
	protected function getCountBy($keyString, $values) {
		$query = $this->getCountQuery($keyString, $values);
		$res = DB::fetchFirst($query);
		return intval($res['countAll']);
	}

	/**
	 * get count of all items in the table
	 *
	 * @return	int
	 */
	public function getCountAll() {
		$query = 'SELECT count(*) AS countAll FROM ' . $this->table . $this->getAdditionalWhere(TRUE);
		$result = DB::fetchFirst($query);
		return intval($result['countAll']);
	}

	/**
	 * getter for query
	 *
	 * @param	string	$keyString
	 * @param	array	$values
	 * @return	string
	 */
	protected function getQuery($keyString, $values) {
		$query = 'SELECT * FROM ' . $this->table . $this->getWhere($keyString, $values) . $this->getGroupBy() . $this->getOrderBy() . $this->getLimit();
		return $query;
	}

	/**
	 * getter for count query
	 *
	 * @param	string	$keyString
	 * @param	array	$values
	 * @return	string
	 */
	protected function getCountQuery($keyString, $values) {
		$query = 'SELECT count(*) AS countAll FROM ' . $this->table . $this->getWhere($keyString, $values);
		return $query;
	}

	/**
	 * getter for keys from key string parts, if keypart is not upper camel case, nothing happend
	 *
	 * @param	array	$keyStringParts
	 * @return	array
	 */
	protected function getKeys($keyStringParts) {
		$keys = array();
		if ($keyStringParts && is_array($keyStringParts)) {
			foreach ($keyStringParts as $part) {
				if (Utils::isUpperCase($part[0])) {
					$part[0] = strtolower($part[0]);
					$keys[] = $this->getColumnName($part);
				}
			}
		}
		return $keys;
	}

	/**
	 * getter for and keys - explodes key string by 'And'
	 *
	 * @param	string	$keyString
	 * @return	array
	 */
	protected function getAndKeys($keyString) {
		$keyStringParts = explode('And', $keyString);
		return $this->getKeys($keyStringParts);
	}

	/**
	 * getter for or keys - explodes key string by 'Or'
	 *
	 * @param	string	$keyString
	 * @return	array
	 */
	protected function getOrKeys($keyString) {
		$keyStringParts = explode('Or', $keyString);
		return $this->getKeys($keyStringParts);
	}

	/**
	 * getter for and/or keys - explodes key string by 'And' / 'Or'
	 *
	 * @param	string	$keyString
	 * @return	array
	 */
	protected function getAndOrKeys($keyString) {
		$keyStringParts = Utils::explodeByArray(array('And', 'Or'), $keyString);
		return $this->getKeys($keyStringParts);
	}

	/**
	 * getter for keys and their glue for where clausule
	 *
	 * @param	string	$keyString
	 * @return	array
	 */
	protected function getKeysAndGlue($keyString) {
		$andOrKeys = $this->getAndOrKeys($keyString);
		$andKeys = $this->getAndKeys($keyString);
		$orKeys = $this->getOrKeys($keyString);
		
		if ($andOrKeys == $andKeys) {
			$orKeys = array();
			$keys = $andKeys;
			$glue = ' AND ';
		} elseif ($andOrKeys == $orKeys) {
			$andKeys = array();
			$keys = $orKeys;
			$glue = ' OR ';
		} else {
			throw new Exception('Not yet imlemented AND and OR in one query', 1307390312);
		}
		return array('keys' => $keys, 'glue' => $glue);
	}

	/**
	 * getter for column name - replace all upper case chars to lowercase and prefix by '_'
	 *
	 * @param	string	$keyString
	 * @return	string
	 */
	protected function getColumnName($keyString) {
		$columnName = '';
		for ($i = 0; $i < strlen($keyString); $i++) {
			$char = $keyString[$i];
			if (Utils::isUpperCase($char)) {
				$columnName .= '_';
			}
			$columnName .= strtolower($char);
		}
		return $columnName;
	}

	/**
	 * getter for where
	 *
	 * @param	string	$keyString
	 * @param	array	$values
	 * @return	string
	 */
	protected function getWhere($keyString, $values) {
		$keys = array();
		if ($keyString != '') {
			$keysAndGlue = $this->getKeysAndGlue($keyString);
			$keys = $keysAndGlue['keys'];
			$glue = $keysAndGlue['glue'];
		}

		if (count($keys) == count($values)) {
			$whereParts = array();
			for ($i = 0; $i < count($keys); $i++) {
				if (is_array($values[$i])) {
					foreach ($values[$i] as &$val) {
						$val = '"' . addslashes($val) . '"';
					}
					$whereParts[] = $keys[$i] . ' IN (' . implode(', ', $values[$i]) . ')';
				} else {
					$whereParts[] = $keys[$i] . ' = "' . addslashes($values[$i]) . '"';
				}
			}

			$where = implode($glue, $whereParts);

			if ($this->additionalWhere) {
				if ($where != '') {
					$where = '(' . $where . ') AND ';
				}
				$where .= $this->getAdditionalWhere();
			}

			if ($where) {
				return ' WHERE ' . $where;
			} else {
				return '';
			}
		}
		throw new Exception('Keys count and values count not match', 1307389592);
	}

	/**
	 * getter for order by
	 *
	 * @return	string
	 */
	protected function getOrderBy() {
		if ($this->orderBy && is_array($this->orderBy)) {
			$orderByParts = array();
			foreach ($this->orderBy as $column => $way) {
				$orderByParts[] = $column . ' ' . (strtolower($way) == 'desc' ? 'DESC' : 'ASC');
			}
			return ' ORDER BY ' . implode(', ', $orderByParts);
		}
		return '';
	}

	/**
	 * setter for order by
	 *
	 * @param	array	$orderBy
	 * @return	void
	 */
	public function setOrderBy($orderBy) {
		if (!is_array($orderBy)) {
			$orderBy = array($orderBy);
		}
		$this->orderBy = $orderBy;
	}

	/**
	 * adds order condition to array
	 *
	 * @param	array	$orderBy
	 * @return	void
	 */
	public function addOrderBy($orderBy) {
		if (!is_array($orderBy)) {
			$orderBy = array($orderBy);
		}
		$this->orderBy = array_merge($orderBy, $this->orderBy);
	}

	/**
	 * getter for limit
	 *
	 * @return	string
	 */
	protected function getLimit() {
		if ($this->limit > 0 && is_numeric($this->limit)) {
			return ' LIMIT ' . $this->limit;
		}
		return '';
	}

	/**
	 * setter for limit
	 *
	 * @param	int	$limit
	 */
	public function setLimit($limit) {
		if (is_numeric($limit)) {
			$this->limit = $limit;
		}
	}

	/**
	 * setter for additional where
	 *
	 * @param	mixed	$where
	 * @return	void
	 */
	public function setAdditionalWhere($where) {
		if (!is_array($where)) {
			throw new Exception('Where have to be array of arrays with keys column, value, and xxx (optional)', 1316289805);
		}

		$this->additionalWhere = array();
		foreach ($where as $one) {
			$this->addAdditionalWhere($one);
		}
	}

	/**
	 * adds additional where
	 *
	 * @param	mixed	$where
	 * @return	void
	 */
	public function addAdditionalWhere($where) {
		if (!is_array($where)) {
			throw new Exception('Where have to be array with keys column, value, and xxx (optional)', 1316289775);
		}

		if (!isset($where['column'])) {
			throw new Exception('Column property is missing in where array', 1316289785);
		}

		if (!isset($where['value'])) {
			throw new Exception('Value property is missing in where array', 1316289795);
		}

		if (!isset($where['xxx'])) {
			$where['xxx'] = '=';
		} else {
			$where['xxx'] = strtoupper($where['xxx']);
		}

		$this->additionalWhere[] = $where;
	}

	protected function getAdditionalWhere($addWhereWord = FALSE) {
		if ($this->additionalWhere && is_array($this->additionalWhere)) {
			$whereParts = array();
			foreach ($this->additionalWhere as $oneWhere) {
				$column = $oneWhere['column'];
				$xxx = $oneWhere['xxx'];
				$value = $oneWhere['value'];
				if (is_array($value)) {
					foreach ($value as &$val) {
						$val = '"' . addslashes($val) . '"';
					}

					if (in_array($xxx, array('=', 'IN'))) {
						$whereParts[] = $column . ' IN (' . implode(', ', $value) . ')';
					} elseif (in_array($xxx, array('!=', 'NOT IN'))) {
						$whereParts[] = $column . ' NOT IN (' . implode(', ', $value) . ')';
					}
				} else {
					$whereParts[] = $column . ' ' . $xxx . ' "' . addslashes($value) . '"';
				}
			}
			$where = implode(' AND ', $whereParts);
			if ($where && $addWhereWord) {
				$where = ' WHERE ' . $where;
			}
			return $where;
		}
	}

	/**
	 * getter for group by
	 *
	 * @return	string
	 */
	protected function getGroupBy() {
		if ($this->groupBy && is_array($this->groupBy)) {
			return ' GROUP BY ' . implode(', ', $this->groupBy);
		}
		return '';
	}

	/**
	 * setter for group by
	 *
	 * @param	array	$groupBy
	 * @return	void
	 */
	public function setGroupBy($groupBy) {
		if (!is_array($groupBy)) {
			$groupBy = array($groupBy);
		}
		$this->groupBy = $groupBy;
	}

	/**
	 * adds group by to array
	 *
	 * @param	array	$groupBy
	 * @return	void
	 */
	public function addGroupBy($groupBy) {
		if (!is_array($groupBy)) {
			$groupBy = array($groupBy);
		}
		$this->groupBy = array_merge($groupBy, $this->groupBy);
	}
}

?>