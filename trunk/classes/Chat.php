<?php

class Chat {
	
	protected static $table = 'message';
	
	public static function addMessage($message) {
		$loggedUser = User::whoIsLogged();
		$params = array(
			'text' => $message,
			'user' => $loggedUser['id'],
		);
		
		$GLOBALS['db']->insert(self::$table, $params);
	}
	
	public static function getMessages() {
		$query = 'SELECT message.*, user.* FROM ' . self::$table . ' LEFT JOIN user ON message.user = user.id ORDER BY message.id DESC LIMIT 100';
		$messages = $GLOBALS['db']->fetchAll($query);
		$messages = array_reverse($messages);
		
		$text = '';
		foreach ($messages as $message) {
			$text .= '<p>' . ($message['username'] ? '<span style="color:' . $message['color'] . ';">' . $message['username'] . ': </span>' : '') . Utils::linkify($message['text']) . '</p>';
		}
		$text = Utils::replaceEmoticonsInText($text);
		return $text;
	}
}

?>