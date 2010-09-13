<?php

require_once('include.php');

$loggedUser = User::whoIsLogged();
if ($loggedUser === null) {
	Utils::redirect('login.php');
}

if ($_POST['create_room']) {
	$room = Room::addRoom(addslashes($_POST['title']), addslashes($_POST['description']));
	Utils::redirect('room.php?id=' . $room);
}

$rooms = Room::getRooms();
$GLOBALS['smarty']->assign('loggedUser', $loggedUser);
$GLOBALS['smarty']->assign('rooms', $rooms);
echo $GLOBALS['smarty']->fetch('rooms.tpl');

?>