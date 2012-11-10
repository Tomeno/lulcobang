<?php

/**
 * class for memcache
 *
 * @author	Michal Lulco <lulco@monogram.sk>
 */
class BangMemcache {

	protected static $instance = NULL;

	protected $memcache = NULL;

	/**
	 * constructor - creates instance of memcache
	 */
	private function  __construct() {
		$this->memcache = new Memcache();
		$connectResult = $this->memcache->connect('localhost','11211');
		if ($connectResult === FALSE) {
			$this->memcache = NULL;
		}
	}

	/**
	 * singleton
	 *
	 * @return	BangMemcache
	 */
	public static function instance() {
		if (self::$instance === NULL) {
			self::$instance = new BangMemcache();
		}
		return self::$instance;
	}

	public function getMemcache() {
		return $this->memcache;
	}
	
	/**
	 * gets value from memcache on key $key
	 *
	 * @param	string	$key
	 * @return	mixed
	 */
	public function get($key) {
		if ($this->useCache()) {
			$memcache = $this->memcache;
			if ($memcache) {
				$result = $this->memcache->get($key);
				$result = unserialize($result);
				return $result;
			}
		}
		return NULL;
	}

	/**
	 * sets value $value to memcache to key $key
	 *
	 * @param	string	$key
	 * @param	mixed	$value
	 * @param	mixed	$flags
	 * @param	mixed	$expire
	 * @return	mixed
	 */
	public function set($key, $value, $flags = NULL, $expire = 0) {
		if ($this->useCache()) {
			$memcache = $this->memcache;
			if ($memcache) {
				if (is_string($expire)) {
					// hadzalo to warning, takze treba nastavit toto
					date_default_timezone_set('Europe/Prague');
					$expire = strtotime($expire);
				}
				$value = serialize($value);
				if (strlen($value) > 1048534) {
					return NULL;
				} else {
					$result = $memcache->set($key, $value, $flags, $expire);
					return $result;
				}
			}
		}
		return NULL;
	}

	/**
	 * checks if can use cache
	 *
	 * @return	boolean
	 */
	protected function useCache() {
		if (isset($_COOKIE['nc']) && $_COOKIE['nc'] == 1) {
			return FALSE;
		}
		if (isset($_GET['nc']) && $_GET['nc'] == 1) {
			return FALSE;
		}
		return TRUE;
	}
}

?>