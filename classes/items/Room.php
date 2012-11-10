<?php

class Room extends LinkableItem {
	
	protected static $table = 'room';
	
	protected static $roomUserTable = 'room_user';

	protected function getPageType() {
		return 'room';
	}

	protected function  getItemAlias() {
		return $this['alias'];
	}

	public static function addRoom($title, $description) {
		$params = array(
			'title' => $title,
			'description' => $description,
		);
		return DB::insert(self::$table, $params);
	}
	
	public static function addUser($user, $room) {
		if (!self::checkUserInRoom($user, $room)) {
			$params = array(
				'user' => $user,
				'room' => $room,
				'last_activity' => time(),
			);
			DB::insert(self::$roomUserTable, $params);
		}
		else {
			self::updateUserLastActivity($user, $room);
		}
	}
	
	public static function updateUserLastActivity($user, $room) {
		$memcacheInstance = BangMemcache::instance();
		if ($memcacheInstance->getMemcache()) {
			$key = 'last_activity_user_' . $user . '_room_' . $room;
			$memcacheInstance->set($key, time(), NULL, '+5 minutes');
		} else {
			$params = array(
				'last_activity' => time(),
			);
			DB::update(self::$roomUserTable, $params, 'user = ' . intval($user) . ' AND room = ' . intval($room));
		}
	}
	
	public static function getUserLastActivityInRoom($user, $room) {
		$memcacheInstance = BangMemcache::instance();
		if ($memcacheInstance->getMemcache()) {
			$key = 'last_activity_user_' . $user . '_room_' . $room;
			return $memcacheInstance->get($key);
		} else {
			$query = 'SELECT last_activity FROM ' . self::$roomUserTable . ' WHERE user = ' . intval($user) . ' AND room = ' . intval($room);
			$user = DB::fetchFirst($query);

			return $user['last_activity'];
		}
	}
	
	public static function checkUserInRoom($user, $room) {
		$query = 'SELECT count(*) AS pocet FROM ' . self::$roomUserTable . ' WHERE user=' . intval($user) . ' AND room=' . intval($room);
		$row = DB::fetchFirst($query);
		return $row['pocet'] ? true : false;
	}
	
	public static function getUsers($room) {
		$query = 'SELECT user.username FROM user AS user LEFT JOIN ' . self::$roomUserTable . ' AS room_user ON user.id = room_user.user WHERE user.cookie_value != "" AND room_user.last_activity > ' . strtotime("-20 seconds") . ' AND room_user.room = ' . intval($room);
		$users = DB::fetchAll($query);
		return $users;
	}
	
	public static function removeUser($user) {
		$query = 'DELETE FROM ' . self::$roomUserTable . ' WHERE user = ' . intval($user);
		DB::query($query);
	}
	
//	public function getGame() {
//		throw new Exception('Room::getGame', 1352571788);
//		$query = 'SELECT * FROM game WHERE room = ' . intval($this['id']) . ' AND status IN (' . Game::GAME_STATUS_CREATED . ', ' . Game::GAME_STATUS_INITIALIZED . ', ' . Game::GAME_STATUS_STARTED . ')';
//		$game = DB::fetchFirst($query);
//		
//		if ($game) {
//			return new Game($game);
//		}
//		return null;
//	}
}

?>