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
		$query = 'SELECT * FROM user WHERE username="' . addslashes($_POST['username']) . '" AND password="' . md5(addslashes($_POST['password'])) . '"';
		$user = $GLOBALS['db']->fetchFirst($query);
		
		if ($user) {
			$cookieValue = md5(time() . $user['id'] . $user['username']);
			$GLOBALS['db']->update('user', array('cookie_value' => $cookieValue), 'id = ' . $user['id']);
			setcookie(self::$cookieName, $cookieValue);
		}
		
		Utils::redirect('index.php');
		
	}
}

?>