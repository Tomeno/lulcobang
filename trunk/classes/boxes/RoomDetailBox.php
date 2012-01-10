<?php

class RoomDetailBox extends AbstractBox {
	
	protected $template = 'room.tpl';
	
	protected function setup() {
		$roomAlias = Utils::get('identifier');
		
		$roomRepository = new RoomRepository();
		$room = $roomRepository->getOneByAlias($roomAlias);

		if ($room) {
			$gameRepository = new GameRepository();
			$game = $gameRepository->getOneByRoom($room['id']);

			$chatBox = new ChatBox();
			$chatBox->setRoom($room);
			MySmarty::assign('chatBox', $chatBox->render());

			if ($game) {
				$gameBox = new GameBox();
				$gameBox->setGame($game);
				MySmarty::assign('gameBox', $gameBox->render());
			}
		} else {
			// TODO 404 room not found
		}
	}
}

?>