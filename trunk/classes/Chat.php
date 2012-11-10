<?php

class Chat {
	
	protected static $table = 'message';
	
	/**
	 * adds message to database
	 *
	 * @param	array	$messageParams	(
	 *		text	string,
	 *		user	int,
	 *		room	int,
	 *		toUser	int,
	 *		localizeKey	string,
	 *		localizeParams	array
	 * )
	 */
	public static function addMessage($messageParams) {
		if (!$messageParams['user']) {
			$loggedUser = LoggedUser::whoIsLogged();
			$messageParams['user'] = $loggedUser['id'];
		}
		$params = array (
			'tstamp' => time(),
			'text' => addslashes($messageParams['text']),
			'user' => intval($messageParams['user']),
			'room' => intval($messageParams['room']),
			'to_user' => intval($messageParams['toUser']),
			'not_to_user' => intval($messageParams['notToUser']),
			'localize_key' => addslashes($messageParams['localizeKey']),
			'localize_params' => serialize($messageParams['localizeParams']),
		);
		
		DB::insert(self::$table, $params);
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
		$colorRepository = new ColorRepository(TRUE);
		$colors = $colorRepository->getAll();

		$colorList = array();
		foreach ($colors as $color) {
			$colorList[$color['id']] = $color;
		}

		$query = 'SELECT user.*, message.*
			FROM ' . self::$table . '
			LEFT JOIN user ON message.user = user.id
			WHERE room=' . intval($room) . ' AND tstamp > ' . intval($time) . ' AND to_user IN (' . intval($toUser) . ', 0)';
		if ($toUser) {
			$query .= ' AND NOT not_to_user IN (' . $toUser . ')';
		}
		
		$query .= ' ORDER BY message.id DESC LIMIT 100';
		$messages = DB::fetchAll($query);
		$messages = array_reverse($messages);

		foreach ($messages as &$message) {
			if ($message['localize_key']) {
				$message['text'] = Localize::getMessage($message['localize_key'], unserialize($message['localize_params']));
			}
			$message['color'] = $colorList[$message['color']]['code'];
		}
		return $messages;
	}
}

?>