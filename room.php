<?php

require_once('include.php');

if (User::whoIsLogged() === null) {
	Utils::redirect('login.php');
}

$actualUrl =  Utils::getActualUrl();

$room = intval($_GET['id']);
if (!$room) {
	Utils::redirect('rooms.php');
}

if ($_POST && trim($_POST['message'])) {
	Chat::addMessage(trim($_POST['message']), $room);
	Utils::redirect($actualUrl);
}

$messages = Chat::getMessages($room);
$emoticons = Emoticons::getEmoticons();

$GLOBALS['smarty']->assign('room', $room);
$GLOBALS['smarty']->assign('messages', $messages);
$GLOBALS['smarty']->assign('emoticonDir', EMOTICONS_DIR);
$GLOBALS['smarty']->assign('emoticons', $emoticons);

$GLOBALS['smarty']->assign('content', $GLOBALS['smarty']->fetch('chat.tpl'));
$GLOBALS['smarty']->assign('bodyAdded', 'onload="JavaScript:timedRefresh(10000, ' . $room . ');"');
echo $GLOBALS['smarty']->fetch('content.tpl');


?>