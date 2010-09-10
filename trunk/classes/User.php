<?php

class User {
	
	protected static $loggedUser = null;
	
	protected static $cookieName = 'bang_user';
	
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
				'color' => strtoupper('#' . dechex(rand(0, 255)) . dechex(rand(0, 255)) . dechex(rand(0, 255))),
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
}

?>