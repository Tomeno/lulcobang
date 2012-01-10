<?php
echo 'ako som sa sem dostal? nemam tu byt, treba zistit a toto zmazat';exit();
require_once('include.php');

$loggedUser = User::whoIsLogged();
if ($loggedUser === null) {
	Utils::redirect('login.php');
}

User::userLogout();

?>