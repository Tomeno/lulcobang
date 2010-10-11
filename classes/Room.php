<?php

class Room extends Item {
	
	protected static $table = 'room';
	
	protected static $roomUserTable = 'room_user';
	
	public static function addRoom($title, $description) {
		$params = array(
			'title' => $title,
			'description' => $description,
		);
		return $GLOBALS['db']->insert(self::$table, $params);
	}
	
	public static function addUser($user, $room) {
		if (!self::checkUserInRoom($user, $room)) {
			$params = array(
				'user' => $user,
				'room' => $room,
				'last_activity' => time(),
			);
			$GLOBALS['db']->insert(self::$roomUserTable, $params);
		}
		else {
			self::updateUserLastActivity($user, $room);
		}
	}
	
	public static function updateUserLastActivity($user, $room) {
		$params = array(
			'last_activity' => time(),
		);
		$GLOBALS['db']->update(self::$roomUserTable, $params, 'user = ' . intval($user) . ' AND room = ' . intval($room));
	}

	public static function checkUserInRoom($user, $room) {
		$query = 'SELECT count(*) AS pocet FROM ' . self::$roomUserTable . ' WHERE user=' . intval($user) . ' AND room=' . intval($room);
		$row = $GLOBALS['db']->fetchFirst($query);
		return $row['pocet'] ? true : false;
	}
	
	public static function getUsers($room) {
		$query = 'SELECT user.username FROM user AS user LEFT JOIN ' . self::$roomUserTable . ' AS room_user ON user.id = room_user.user WHERE user.cookie_value != "" AND room_user.last_activity > ' . strtotime("-20 seconds") . ' AND room_user.room = ' . intval($room);
		$users = $GLOBALS['db']->fetchAll($query);
		return $users;
	}
	
	public static function removeUser($user) {
		$query = 'DELETE FROM ' . self::$roomUserTable . ' WHERE user = ' . intval($user);
		$GLOBALS['db']->query($query);
	}
	
	public static function getUserLastActivityInRoom($user, $room) {
		$query = 'SELECT last_activity FROM ' . self::$roomUserTable . ' WHERE user = ' . intval($user) . ' AND room = ' . intval($room);
		$user = $GLOBALS['db']->fetchFirst($query);
		
		return $user['last_activity'];
	}
	
	public function getGame() {
		$query = 'SELECT * FROM game WHERE room = ' . intval($this['id']) . ' AND status IN (' . Game::GAME_STATUS_CREATED . ', ' . Game::GAME_STATUS_STARTED . ')';
		$game = $GLOBALS['db']->fetchFirst($query);
		
		if ($game) {
			return new Game($game);
		}
		return null;
	}
}

?>