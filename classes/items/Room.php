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
		return DB::insert(DB_PREFIX . self::$table, $params);
	}
	
	public static function addUser($user, $room) {
		if (!self::checkUserInRoom($user, $room)) {
			$params = array(
				'user' => $user,
				'room' => $room,
				'last_activity' => time(),
			);
			DB::insert(DB_PREFIX . self::$roomUserTable, $params);
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
			DB::update(DB_PREFIX . self::$roomUserTable, $params, 'user = ' . intval($user) . ' AND room = ' . intval($room));
		}
	}
	
	public static function getUserLastActivityInRoom($user, $room) {
		$memcacheInstance = BangMemcache::instance();
		if ($memcacheInstance->getMemcache()) {
			$key = 'last_activity_user_' . $user . '_room_' . $room;
			return $memcacheInstance->get($key);
		} else {
			$query = 'SELECT last_activity FROM ' . DB_PREFIX . self::$roomUserTable . ' WHERE user = ' . intval($user) . ' AND room = ' . intval($room);
			$user = DB::fetchFirst($query);

			return $user['last_activity'];
		}
	}
	
	public static function checkUserInRoom($user, $room) {
		$query = 'SELECT count(*) AS pocet FROM ' . DB_PREFIX . self::$roomUserTable . ' WHERE user=' . intval($user) . ' AND room=' . intval($room);
		$row = DB::fetchFirst($query);
		return $row['pocet'] ? true : false;
	}
	
	public static function getUsers($room) {
		$query = 'SELECT user.username FROM ' . DB_PREFIX . 'user AS user LEFT JOIN ' . DB_PREFIX . self::$roomUserTable . ' AS room_user ON user.id = room_user.user WHERE user.cookie_value != "" AND room_user.last_activity > ' . strtotime("-20 seconds") . ' AND room_user.room = ' . intval($room);
		$users = DB::fetchAll($query);
		return $users;
	}
	
	public static function removeUser($user) {
		$query = 'DELETE FROM ' . DB_PREFIX . self::$roomUserTable . ' WHERE user = ' . intval($user);
		DB::query($query);
	}
}

?>