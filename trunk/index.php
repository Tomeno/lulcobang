<?php

require_once('include.php');

if (User::whoIsLogged() === null) {
	Utils::redirect('login.php');
}

Utils::redirect('rooms.php');

?>