<?php

class ChatBox extends AbstractBox {

	protected $template = 'chat.tpl';

	protected $room = NULL;

	protected function setup() {
		$loggedUser = LoggedUser::whoIsLogged();
		if ($this->room !== NULL) {
			Room::addUser($loggedUser['id'], $this->room['id']);

			$messages = Chat::getMessages($this->room['id'], $loggedUser['id']);

			MySmarty::assign('loggedUser', $loggedUser);
			MySmarty::assign('messages', $messages);
			MySmarty::assign('users', Room::getUsers($this->room['id']));
			MySmarty::assign('emoticonDir', EMOTICONS_DIR);
			MySmarty::assign('emoticons', Emoticons::getEmoticons());
			MySmarty::assign('room', $this->room);
		}
	}

	public function setRoom(Room $room) {
		$this->room = $room;
	}
}

?>