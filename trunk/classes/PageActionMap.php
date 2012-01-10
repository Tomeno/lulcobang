<?php

class PageActionMap {

	protected static $pageActionMap = array(
		'miestnosti' => 'rooms',
		'miestnost' => 'rooms',
		'prihlasenie' => 'login',
	);

	public static function getActionByPage($page) {
		if (array_key_exists($page, self::$pageActionMap)) {
			return self::$pageActionMap[$page];
		}
		return 'index';
	}
}

?>