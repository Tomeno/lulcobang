<?php

/**
 * localization for messages
 */
class Localize {

	protected static $localizationMap = NULL;

	protected static $dirs = array('localize');

	public static function init() {
		$cacheFileName = dirname(__FILE__) . '/../tmp/cache/localize';
		if (file_exists($cacheFileName)) {
			$file = fopen($cacheFileName, 'r');
			$data = fread($file, filesize($cacheFileName));
			fclose($file);
			self::$localizationMap = unserialize($data);
		} else {
			if (self::$localizationMap === NULL) {
				self::$localizationMap = array();
				foreach (self::$dirs as $dir) {
					self::readDir($dir);
				}
				$file = fopen($cacheFileName, 'w');
				fwrite($file, serialize(self::$localizationMap));
				fclose($file);
			}
		}
	}

	protected static function readDir($dir) {
		echo $dirname = dirname(__FILE__) . '/../' . $dir . '/';
		if (file_exists($dirname) && is_dir($dirname)) {
			$directory = opendir($dirname);
			while ($item = readdir($directory)) {
				if (($item != ".") && ($item != "..") && ($item != ".svn")) {
					$fullPath = $dirname . $item;

					if (is_dir($fullPath)) {
						self::readDir($dir . '/' . $item);
					} else {
						$pathinfo = pathinfo($fullPath);

						if ($pathinfo['extension']) {
							$language = $pathinfo['extension'];
						} else {
							throw new Exception('File ' . $fullPath . ' has no extension', 1316281825);
						}

						$filesize = filesize($fullPath);
						if ($filesize) {
							$file = fopen($fullPath, 'r');
							$data = fread($file, $filesize);
							$rows = explode("\n", $data);
							foreach ($rows as $row) {
								if (trim($row)) {
									$translates = explode('###', $row);
									$key = $translates[0];
									$value = $translates[1];
									if (!isset(self::$localizationMap[$language][$key])) {
										self::$localizationMap[$language][$key] = trim($value);
									}
								}
							}
							fclose($file);
						}
					}
				}
			}
		}
	}

	/**
	 * getter for message for key $key
	 *
	 * @param	$key	localize key
	 * @param	$params	variable values
	 * @return	string
	 * @throws	Exception
	 */
	public static function getMessage($key, $params = array()) {
		if ($key) {
			$language = Utils::getLanguage();
			$lang = $language['shortcut'];
			
			self::init();
			if (isset(self::$localizationMap[$lang][$key])) {
				$message = self::$localizationMap[$lang][$key];
			} elseif (isset(self::$localizationMap['default'][$key])) {
				$message = self::$localizationMap['default'][$key];
			} else {
				throw new Exception('Key ' . $key . ' not found in localization files', 1327603452);
			}

			if ($params) {
				foreach ($params as $param) {
					$message = sprintf($message, $param);
				}
			}
			return $message;
		} else {
			throw new Exception('Key is not set in localization.', 1316280033);
		}
	}
}

?>