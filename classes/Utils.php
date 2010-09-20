<?php

class Utils {
	
	/**
	 * redirects to $url
	 *
	 * @param string $url
	 */
	public static function redirect($url) {
		header("Location: $url");
		exit();
	}
	
	/**
	 * getter for actual url
	 *
	 * @return string
	 */
	public static function getActualUrl() {
		return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	
	/**
	 * replaces emoticons in text
	 *
	 * @param string $text
	 * @return string
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
}

?>