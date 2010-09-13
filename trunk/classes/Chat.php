<?php

class Chat {
	
	protected static $table = 'message';
	
	/**
	 * adds message to database
	 *
	 * @param string $message
	 * @param int $room
	 */
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
	
	/**
	 * getter for messages
	 *
	 * @param int $room
	 * @return array
	 */
	public static function getMessages($room) {
		$query = 'SELECT message.*, user.* FROM ' . self::$table . ' LEFT JOIN user ON message.user = user.id WHERE room=' . intval($room) . ' ORDER BY message.id DESC LIMIT 100';
		$messages = $GLOBALS['db']->fetchAll($query);
		$messages = array_reverse($messages);
		
		return $messages;
	}
}

?>