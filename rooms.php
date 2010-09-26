<?php

require_once('include.php');

$loggedUser = User::whoIsLogged();
if ($loggedUser === null) {
	Utils::redirect('login.php');
}

if ($_POST['create_room'] && $loggedUser['admin']) {
	$room = Room::addRoom(addslashes($_POST['title']), addslashes($_POST['description']));
	Utils::redirect('room.php?id=' . $room);
}

$roomRepository = new RoomRepository();
$rooms = $roomRepository->getAll();

$GLOBALS['smarty']->assign('loggedUser', $loggedUser);
$GLOBALS['smarty']->assign('rooms', $rooms);
$GLOBALS['smarty']->assign('content', $GLOBALS['smarty']->fetch('rooms.tpl'));
echo $GLOBALS['smarty']->fetch('content.tpl');

?>