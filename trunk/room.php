<?php

require_once('include.php');

$loggedUser = User::whoIsLogged();
if ($loggedUser === null) {
	Utils::redirect('login.php');
}

$actualUrl =  Utils::getActualUrl();

$room = intval($_GET['id']);
if (!$room) {
	Utils::redirect('rooms.php');
}

Room::addUser($loggedUser['id'], $room);

if ($_POST && trim($_POST['message'])) {
	
	if (strpos($_POST['message'], '.') === 0) {
		$commandResult = Command::execute($_POST['message'], $room);
		Chat::addMessage($commandResult, $room);
	}
	else {
		Chat::addMessage(trim($_POST['message']), $room);
	}
	Room::updateUserLastActivity($loggedUser['id'], $room);
	Utils::redirect($actualUrl);
}

$gameRepository = new GameRepository();
$game = $gameRepository->getOneByRoom($room);

if ($game) {
	$GLOBALS['smarty']->assign('game', $game);
}

$messages = Chat::getMessages($room);
$emoticons = Emoticons::getEmoticons();

$GLOBALS['smarty']->assign('loggedUser', $loggedUser);
$GLOBALS['smarty']->assign('room', $room);
$GLOBALS['smarty']->assign('messages', $messages);
$GLOBALS['smarty']->assign('users', Room::getUsers($room));
$GLOBALS['smarty']->assign('emoticonDir', EMOTICONS_DIR);
$GLOBALS['smarty']->assign('emoticons', $emoticons);

$GLOBALS['smarty']->assign('content', $GLOBALS['smarty']->fetch('room.tpl'));
$GLOBALS['smarty']->assign('bodyAdded', 'onload="JavaScript:timedRefresh(10000, ' . $room . ');"');
echo $GLOBALS['smarty']->fetch('content.tpl');

?>