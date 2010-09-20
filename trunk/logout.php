<?php

require_once('include.php');

$loggedUser = User::whoIsLogged();
if ($loggedUser === null) {
	Utils::redirect('login.php');
}

User::userLogout();

?>