<?php

class ChatAction extends AbstractAction {

	public function getContent() {
		$loggedUser = LoggedUser::whoIsLogged();
		$room = intval($_GET['id']);

		$roomRepository = new RoomRepository();
		$r = $roomRepository->getOneById($room);
		if ($r) {
			Room::addUser($loggedUser['id'], $room);

			if ($_POST && trim($_POST['message'])) {
				if (strpos($_POST['message'], '.') === 0) {
					$commandResult = Command::execute($_POST['message'], $game);
				}
				else {
					Chat::addMessage(trim($_POST['message']), $room);
				}
				Room::updateUserLastActivity($loggedUser['id'], $room);
				Utils::redirect(Utils::getActualUrl(), FALSE);
			}

			$messages = Chat::getMessages($room, $loggedUser['id']);

			MySmarty::assign('loggedUser', $loggedUser);
			MySmarty::assign('messages', $messages);
			MySmarty::assign('users', Room::getUsers($room));
			MySmarty::assign('emoticonDir', EMOTICONS_DIR);
			MySmarty::assign('emoticons', Emoticons::getEmoticons());
			MySmarty::assign('bodyAdded', 'onload="JavaScript:timedRefresh(10000, ' . $room . ');"');

			return MySmarty::fetch('chat.tpl');
		} else {
			Utils::redirect('rooms.html');
		}
	}
}

?>