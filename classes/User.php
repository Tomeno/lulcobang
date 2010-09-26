<?php

class User extends Item {
	
	protected static $loggedUser = null;
	
	protected static $cookieName = 'bang_user';
	
	const SYSTEM = 1;
	
	public function __construct($user) {
		parent::__construct($user);
	}

	public static function whoIsLogged() {
		if (self::$loggedUser === null) {
			if ($_COOKIE[self::$cookieName]) {
				$query = 'SELECT * FROM user WHERE cookie_value="' . addslashes($_COOKIE['bang_user']) . '"';
				self::$loggedUser = $GLOBALS['db']->fetchFirst($query);
			}
		}
		return self::$loggedUser;
	}
	
	public static function userLogin() {
		$username = addslashes($_POST['username']);
		$password = md5(addslashes($_POST['password']));
		$query = 'SELECT * FROM user WHERE username="' . $username . '"';
		$userExist = $GLOBALS['db']->fetchFirst($query);
		if (!$userExist) {
			$params = array(
				'username' => $username,
				'password' => $password,
				'color' => strtoupper('#' . str_pad(dechex(rand(0, 255)), 2, 0, STR_PAD_LEFT) . str_pad(dechex(rand(0, 255)), 2, 0, STR_PAD_LEFT) . str_pad(dechex(rand(0, 255)), 2, 0, STR_PAD_LEFT)),
			);
			$GLOBALS['db']->insert('user', $params);
		}
		
		$query = 'SELECT * FROM user WHERE username="' . $username . '" AND password="' . $password . '"';
		$user = $GLOBALS['db']->fetchFirst($query);
		
		if ($user) {
			$cookieValue = md5(time() . $user['id'] . $user['username']);
			$GLOBALS['db']->update('user', array('cookie_value' => $cookieValue), 'id = ' . $user['id']);
			setcookie(self::$cookieName, $cookieValue);
			Utils::redirect('rooms.php');
		}
		
		return 'Nesprávne heslo.';
	}
	
	public static function userLogout() {
		$loggedUser = User::whoIsLogged();
		Room::removeUser($loggedUser['id']);
		setcookie(self::$cookieName, "", time() - 3600);
		$GLOBALS['db']->update('user', array('cookie_value' => ''), 'id = ' . $loggedUser['id']);
		
		Utils::redirect('index.php');
	}
	

}

?>