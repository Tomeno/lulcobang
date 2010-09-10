<?php

class Chat {
	
	protected static $table = 'message';
	
	public static function addMessage($message, $room) {
		$loggedUser = User::whoIsLogged();
		$params = array(
			'tstamp' => time(),
			'text' => $message,
			'user' => $loggedUser['id'],
			'room' => intval($room),
		);
		
		$GLOBALS['db']->insert(self::$table, $params);
	}
	
	public static function getMessages($room) {
		$query = 'SELECT message.*, user.* FROM ' . self::$table . ' LEFT JOIN user ON message.user = user.id WHERE room=' . intval($room) . ' ORDER BY message.id DESC LIMIT 100';
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