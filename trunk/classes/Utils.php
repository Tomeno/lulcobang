<?php

class Utils {
	
	public static function redirect($url) {
		header("Location: $url");
		exit();
	}
	
	public static function getActualUrl() {
		return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	
	public static function replaceEmoticonsInText($text) {
		$emoticons = Emoticons::getEmoticons();
		
		foreach ($emoticons as $emoticon) {
			foreach ($emoticon['alternatives'] as $alternative) {
				$text = str_replace($alternative, '<img src="' . EMOTICONS_DIR . $emoticon['image'] . '" />', $text);
			}
		}
		return $text;
	}
	
	public static function linkify($text) {
		return preg_replace('@https?://(.*?)(/(.*?))?(?=\s|$)@u', '<a href="\0" onclick=\'window.open("\0", "_blank"); return false;\'>\1</a>', $text);
	}
}

?>