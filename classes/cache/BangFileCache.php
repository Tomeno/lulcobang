<?php

class BangFileCache {

	/**
	 * gets data from file by $key
	 *
	 * @param	string	$key
	 * @return	mixed
	 */
	public static function get($key) {
		if (self::useCache()) {
			$cacheFileName = self::getCacheFileName($key);

			if (file_exists($cacheFileName)) {
				$file = fopen($cacheFileName, 'r');
				$data = fread($file, filesize($cacheFileName));
				fclose($file);
				return $data;
			} else {
				return NULL;
			}
		}
		return FALSE;
	}

	/**
	 * sets data to file with name $key
	 *
	 * @param	string	$key
	 * @param	mixed	$value
	 * @return	int
	 */
	public static function set($key, $value) {
		if (self::useCache()) {
			$cacheFileName = self::getCacheFileName($key);

			$file = fopen($cacheFileName, 'w');
			$result = fwrite($file, $value);
			fclose($file);
			chmod($cacheFileName, 0777);

			return $result;
		}
		return FALSE;
	}

	/**
	 * checks if can use cache
	 *
	 * @return	boolean
	 */
	protected static function useCache() {
		if (isset($_COOKIE['nc']) && $_COOKIE['nc'] == 1) {
			return FALSE;
		}
		if (isset($_GET['nc']) && $_GET['nc'] == 1) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * gets cache filename
	 *
	 * @param	string	$key
	 * @return	string
	 */
	protected static function getCacheFileName($key) {
		$addrname = dirname(__FILE__) . '/../../tmp/file_cache/';
		if (!file_exists($addrname)) {
			mkdir($addrname);
			chmod($addrname, 0777);
		}
		$cacheFileName = $addrname . $key;
		return $cacheFileName;
	}
}

?>