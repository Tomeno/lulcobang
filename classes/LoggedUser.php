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
		$errors = array();
		$hash = addslashes(Utils::get('hash'));
		if ($hash) {
			$userRepository = new UserRepository();
			$user = $userRepository->getOneByHash($hash);
		} else {
			if (Utils::post('username') != '') {
				$username = addslashes(Utils::post('username'));
				if (!ctype_alnum($username)) {
					$errors['username'] = 'V používateľskom mene môžeš použiť len alfanumerické znaky';	// TODO localize
				}
			} else {
				$errors['username'] = 'Musíš vyplniť používateľské meno';	// TODO localize
			}
			if (Utils::post('password') != '') {
				$password = md5(addslashes(Utils::post('password')));
			} else {
				$errors['password'] = 'Musíš vyplniť heslo';	// TODO localize
			}
			
			if (empty($errors)) {
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

					$user = new User($params);
					$user = $user->save(TRUE);
				} elseif ($userExist['password'] != $password) {
					$errors['password'] = 'Nesprávne heslo';
				} else {
					$user = $userExist;
				}
			}
		}
		
		if ($user && empty($errors)) {
			// TODO po prihlaseni treba nejako zmazat v memcachi query, ktora vybera usera podla cookie_value
			// lebo teraz to stale vracia vysledok z memcache -> ked sa prihlasim v dvoch browsroch, v obidvoch to funguje
			// neodhlasi ma z toho prveho
			
			$cookieValue = md5(time() . $user['id'] . $user['username']);
			DB::update(DB_PREFIX . 'user', array('cookie_value' => $cookieValue), 'id = ' . $user['id']);
			
			$expire = Utils::post('remember') == 1 ? strtotime('+1 year') : 0;
			setcookie(self::$cookieName, $cookieValue, $expire, '/');
			return TRUE;
		} else {
			return $errors;
		}
	}

	/**
	 * logout the actual user
	 *
	 * @return	void
	 */
	public static function userLogout() {
		setcookie(self::$cookieName, "", time() - 3600, '/');
	}
}

?>