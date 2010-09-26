<?php

class Chat {
	
	protected static $table = 'message';
	
	/**
	 * adds message to database
	 *
	 * @param string $message
	 * @param int $room
	 * @param int $user
	 * @param int $toUser
	 */
	public static function addMessage($message, $room, $user = 0, $toUser = 0) {
		if (!$user) {
			$loggedUser = User::whoIsLogged();
			$user = $loggedUser['id'];
		}
		$params = array(
			'tstamp' => time(),
			'text' => $message,
			'user' => $user,
			'room' => intval($room),
			'to_user' => intval($toUser),
		);
		
		$GLOBALS['db']->insert(self::$table, $params);
	}
	
	/**
	 * getter for messages
	 *
	 * @param int $room
	 * @param int $user
	 * @param int $time
	 * @return array
	 */
	public static function getMessages($room, $toUser = 0, $time = 0) {
		$query = 'SELECT message.*, user.* FROM ' . self::$table . ' LEFT JOIN user ON message.user = user.id WHERE room=' . intval($room) . ' AND tstamp > ' . intval($time) . ' AND to_user IN (' . $toUser . ', 0) ORDER BY message.id DESC LIMIT 100';
		$messages = $GLOBALS['db']->fetchAll($query);
		$messages = array_reverse($messages);
		
		return $messages;
	}
}

?>