<?php

abstract class Item extends ArrayObject {
	
	public function offsetGet($key) {
		if (property_exists($this, $key)) {
			return $this->$key;
		}
		else {
			$method = 'get' . ucfirst($key);
			if (method_exists($this, $method)) {
				return $this->$method();
			}
			return parent::offsetGet($key);
		}
	}
}

?>