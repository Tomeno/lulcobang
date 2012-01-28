<?php

abstract class Item extends ArrayObject {
	
	protected $additionalFields = array();

	public function offsetGet($key) {
		if (property_exists($this, $key)) {
			return $this->$key;
		} else {
			$method = 'get' . ucfirst($key);
			if (method_exists($this, $method)) {
				return $this->$method();
			}
			return parent::offsetGet($key);
		}
	}

	public function save($returnModifiedObject = FALSE) {
		$params = array();
		$update = FALSE;
		foreach ($this as $name => $value) {
			if ($name == 'id') {
				$update = TRUE;
			} elseif ($name == 'additionalFields') {
				continue;
			} else {
				$params[$name] = $value;
			}
		}

		if ($update === TRUE) {
			DB::update($this->getTable(), $params, 'id = ' . intval($this['id']));
			$params['id'] = $this['id'];
		} else {
			$id = DB::insert($this->getTable(), $params);
			$params['id'] = $id;
		}

		if ($returnModifiedObject === TRUE) {
			$className = get_class($this);
			return new $className($params);
		}
	}

	/**
	 * adds additional field to item
	 *
	 * @param	string	$key
	 * @param	mixed	$value
	 * @return	void
	 */
	public function addAdditionalField($key, $value) {
		$this->additionalFields[$key] = $value;
	}

	/**
	 * getter for additional field
	 *
	 * @param	string	$key
	 * @return	mixed
	 */
	public function getAdditionalField($key) {
		if (isset($this->additionalFields[$key])) {
			return $this->additionalFields[$key];
		} else {
			return NULL;
		}
	}

	/**
	 * getter for all additional fields
	 *
	 * @return	array
	 */
	public function getAdditionalFields() {
		return $this->additionalFields;
	}

	/**
	 * getter for repository name
	 *
	 * @return	string
	 */
	public function getRepository() {
		return get_class($this) . 'Repository';
	}

	/**
	 * getter for table name
	 *
	 * @return	string
	 */
	public function getTable() {
		$repositoryClassName = $this->getRepository();
		$repository = new $repositoryClassName();
		return $repository->getTable();
	}

}

?>