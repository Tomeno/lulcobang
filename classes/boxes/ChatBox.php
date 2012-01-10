<?php

class ChatBox extends AbstractBox {

	protected $template = 'chat.tpl';

	protected $room = NULL;

	protected function setup() {
		$loggedUser = LoggedUser::whoIsLogged();
		if ($this->room !== NULL) {
			Room::addUser($loggedUser['id'], $this->room['id']);

			if ($_POST && trim($_POST['message'])) {
				if (strpos($_POST['message'], '.') === 0) {
					$commandResult = Command::execute($_POST['message'], $game);
				}
				else {
					Chat::addMessage(trim($_POST['message']), $this->room['id']);
				}
				Room::updateUserLastActivity($loggedUser['id'], $this->room['id']);
				Utils::redirect(Utils::getActualUrl(), FALSE);
			}

			$messages = Chat::getMessages($this->room, $loggedUser['id']);

			MySmarty::assign('loggedUser', $loggedUser);
			MySmarty::assign('messages', $messages);
			MySmarty::assign('users', Room::getUsers($this->room['id']));
			MySmarty::assign('emoticonDir', EMOTICONS_DIR);
			MySmarty::assign('emoticons', Emoticons::getEmoticons());
			MySmarty::assign('bodyAdded', 'onload="JavaScript:timedRefresh(10000, ' . $this->room['id'] . ');"');
		}
	}

	public function setRoom(Room $room) {
		$this->room = $room;
	}
}

?>