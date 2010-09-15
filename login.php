<?php

require_once('include.php');

if (User::whoIsLogged()) {
	Utils::redirect('rooms.php');
}

if ($_POST['login']) {
	
	$error = User::userLogin();
	$GLOBALS['smarty']->assign('error', $error);
}

$GLOBALS['smarty']->assign('content', $GLOBALS['smarty']->fetch('login.tpl'));
echo $GLOBALS['smarty']->fetch('content.tpl');

?>