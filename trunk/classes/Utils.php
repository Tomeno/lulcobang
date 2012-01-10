<?php

/**
 * class for Utils
 */
class Utils {
	
	/**
	 * redirects to $url
	 *
	 * @param	string	$url
	 * @param	boolean	$absolute
	 * @return	void
	 */
	public static function redirect($url, $absolute = TRUE) {
		if ($absolute) {
			$url = BASE_URL . $url;
		}
		header("Location: $url");
		exit();
	}
	
	/**
	 * getter for actual url
	 *
	 * @return	string
	 */
	public static function getActualUrl() {
		return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	/**
	 * getter for actual url without get parameters
	 * 
	 * @return	string
	 */
	public static function getActualUrlWithoutGetParameters() {
		$actualUrl = self::getActualUrl();
		$questionMarkPosition = strpos($actualUrl, '?');
		if ($questionMarkPosition !== FALSE) {
			$actualUrl = substr($actualUrl, 0, $questionMarkPosition);
		}
		return $actualUrl;
	}


	/**
	 * getter for site language
	 *
	 * @note	zatial nemam tucha co to bude robit :)
	 * @return	string
	 */
	public static function getSiteLanguage() {
		return '';
	}

	/**
	 * getter for base url
	 *
	 * @return	string
	 */
	public static function getBaseUrl() {
		return BASE_URL;
	}

	/**
	 * replaces emoticons in text
	 *
	 * @param	string	$text
	 * @return	string
	 */
	public static function replaceEmoticonsInText($text) {
		$emoticons = Emoticons::getEmoticons();
		
		foreach ($emoticons as $emoticon) {
			foreach ($emoticon['alternatives'] as $alternative) {
				$text = str_replace($alternative, '<img src="' . EMOTICONS_DIR . $emoticon['image'] . '" alt="" />', $text);
			}
		}
		return $text;
	}

	/**
	 * checks if string is upper case
	 *
	 * @param	string	$string
	 * @return	boolean
	 */
	public static function isUpperCase($string) {
		if ($string === strtoupper($string)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * checks if string is lower case
	 *
	 * @param	string	$string
	 * @return	boolean
	 */
	public static function isLowerCase($string) {
		if ($string === strtolower($string)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * explodes string by more delimiters
	 *
	 * @param	mixed	$delimiters
	 * @param	string	$string
	 * @return	array
	 */
	public static function explodeByArray($delimiters, $string) {
		$delimiter = '###delimiter###';
		$string = str_replace($delimiters, $delimiter, $string);
		return explode($delimiter, $string);
	}

	/**
	 * wrapper over $_GET
	 *
	 * @param	string|NULL	$key
	 * @return	mixed|NULL
	 */
	public static function get($key = NULL) {
		if ($_GET) {
			if ($key !== NULL && isset($_GET[$key])) {
				return addslashes($_GET[$key]);
			} elseif ($key === NULL) {
				return $_GET;
			} else {
				return NULL;
			}
		}
		return NULL;
		
	}

	/**
	 * wrapper over $_POST
	 *
	 * @param	string|NULL	$key
	 * @return	mixed|NULL
	 */
	public static function post($key = NULL) {
		if ($_POST) {
			if ($key !== NULL && isset($_POST[$key])) {
				return addslashes($_POST[$key]);
			} elseif ($key === NULL) {
				return $_POST;
			}
			else {
				return NULL;
			}
		}
		return NULL;
	}
}

?>