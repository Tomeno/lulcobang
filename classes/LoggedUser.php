<?php

/**
 * class for LoggedUser
 */
class LoggedUser {

	/**
	 *
	 * @var	User
	 */
	protected static $loggedUser = NULL;

	protected static $cookieName = 'bang_user';

	/**
	 * who is actualy logged
	 * 
	 * @return	User|NULL
	 */
	public static function whoIsLogged() {
		if (self::$loggedUser === NULL) {
			if (isset($_COOKIE[self::$cookieName]) && $_COOKIE[self::$cookieName] !== '') {
				$userRepository = new UserRepository();
				self::$loggedUser = $userRepository->getOneByCookieValue(addslashes($_COOKIE[self::$cookieName]));
			}
		}
		return self::$loggedUser;
	}

	/**
	 * log the user - if not exists register him/her and then log the user
	 *
	 * @return	void
	 */
	public static function userLogin() {
		$hash = addslashes(Utils::get('hash'));
		if ($hash) {
			$userRepository = new UserRepository();
			$user = $userRepository->getOneByHash($hash);
		} else {
			$username = addslashes(Utils::post('username'));
			$password = md5(addslashes(Utils::post('password')));
			$userRepository = new UserRepository();
			$userExist = $userRepository->getOneByUsername($username);

			if ($userExist === NULL) {
				$colorRepository = new ColorRepository();
				$count = $colorRepository->getCountAll();
				$rand = rand(1, $count);

				$params = array(
					'username' => $username,
					'password' => $password,
					'color' => $rand,
				);
				DB::insert('user', $params);
			}
			$user = $userRepository->getOneByUsernameAndPassword($username, $password);
		}
		
		if ($user) {
			$cookieValue = md5(time() . $user['id'] . $user['username']);
			DB::update('user', array('cookie_value' => $cookieValue), 'id = ' . $user['id']);
			setcookie(self::$cookieName, $cookieValue, NULL, '/');

			Utils::redirect(Utils::getActualUrlWithoutGetParameters(), FALSE);
		}
	}

	/**
	 * logout the actual user
	 *
	 * @return	void
	 */
	public static function userLogout() {
		$loggedUser = self::whoIsLogged();
		// TODO add back
//		Room::removeUser($loggedUser['id']);
		setcookie(self::$cookieName, "", time() - 3600, '/');
	}
}

?>