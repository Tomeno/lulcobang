<?php

/**
 * class for Autoload
 */
class Autoload {
	
	protected static $classes = NULL;

	protected static $dirs = array('classes');


	/**
	 * scaner for dirs creates array class => path
	 *
	 * @return	void
	 */
	public static function scanDirs() {
		$dirs = self::$dirs;

		if ($dirs !== NULL && is_array($dirs)) {
			$classes = array();
			foreach ($dirs as $dir) {
				$dir = trim($dir);
				if ($dir !== '') {
					$dir = dirname(__FILE__) . '/../' . $dir;
					$classes = array_merge($classes, self::scanDir($dir));
				}
			}
			self::$classes = $classes;
			self::saveCache($classes);
		}
	}

	/**
	 * getter for cache file name
	 *
	 * @return	string
	 */
	protected static function getCacheFileName() {
		return dirname(__FILE__) . '/../tmp/cache/classes';
	}

	/**
	 * clear cache
	 *
	 * @return	void
	 */
	public static function clearCache() {
		$filename = self::getCacheFileName();
		unlink($filename);
	}

	/**
	 * save cache
	 *
	 * @param	array	$classes
	 * @return	void
	 */
	protected static function saveCache($classes) {
		$filename = self::getCacheFileName();
		$file = fopen($filename, 'w');
		fwrite($file, serialize($classes));
		fclose($file);
	}

	/**
	 * load cache
	 *
	 * @return	array|NULL
	 */
	protected static function loadCache() {
		$filename = self::getCacheFileName();
		if (file_exists($filename)) {
			$file = fopen($filename, 'r');
			$data = fread($file, filesize($filename));
			fclose($file);
			return unserialize($data);
		}
		return NULL;
	}

	/**
	 * scan one dir recursively
	 *
	 * @param	string	$directory
	 * @return	array
	 */
	protected static function scanDir($directory) {
		$dir = dir($directory);
		if (!$dir) {
			return array();
		}
		$classes = array();
		while (false !== ($entry = $dir->read())) {
			if (in_array($entry, array('.', '..', '.svn'))) continue;

			$fullPath = $directory . '/' . 	$entry;
			if (is_dir($fullPath)) {
				$classes = array_merge($classes, self::scanDir($fullPath));
			}

			if ($entry[0] >= 'A' && $entry[0] <= 'Z') {
				$ext = end(explode('.', $entry));
				if (in_array($ext, array('php'))) {
					$className = str_replace('.' . $ext, '', $entry);
					$classes[$className] = $fullPath;
				}
			}
		}
		$dir->close();
		return $classes;
	}

	/**
	 * gets data - initialize self::$classes
	 *
	 * @return	void
	 */
	protected static function getData() {
		$classes = self::loadCache();
		if ($classes !== NULL) {
			self::$classes = $classes;
		} else {
			self::scanDirs();
		}
	}

	/**
	 * getter for classes
	 *
	 * @return	array
	 */
	public static function getClasses() {
		if (self::$classes === NULL) {
			self::getData();
		}
		return self::$classes;
	}
}

?>