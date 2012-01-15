<?php

/**
 * localization for messages
 */
class Localize {

	protected static $localizationMap = NULL;

	protected static $dirs = array('localize');

	public static function init() {
		if (self::$localizationMap === NULL) {
			$localizationMap = array();
			foreach (self::$dirs as $dir) {
				$dirname = dirname(__FILE__) . '/../' . $dir . '/';
				if (file_exists($dirname) && is_dir($dirname)) {
					$directory = opendir($dirname);
					while ($item = readdir($directory)) {
						if (($item != ".") && ($item != "..") && ($item != ".svn")) {
							$fullPath = $dirname . $item;
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
										if (!isset($localizationMap[$ext][$language][$key])) {
											$localizationMap[$ext][$language][$key] = $value;
										}
									}
								}
								fclose($file);
							}
						}
					}
				}
			}
			self::$localizationMap = $localizationMap;
		}
	}

	/**
	 * getter for message from extension $ext for key $key and language $language
	 *
	 * @return	string
	 * @throws	Exception
	 */
	public static function getMessage($key, $params = array()) {
		if ($key) {
			//echo $key;
			$language = Utils::getLanguage();
			$lang = $language['shortcut'];
			
			self::init();
			if (isset(self::$localizationMap[$ext][$lang][$key])) {
				$message = self::$localizationMap[$ext][$lang][$key];
			} else {
				$message = self::$localizationMap[$ext]['default'][$key];
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