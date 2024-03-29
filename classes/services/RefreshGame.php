<?php

class RefreshGame {
	
	public function main() {
		$gameId = intval(Utils::post('game'));
		$roomId = intval(Utils::post('room'));
		
		$gameRepository = new GameRepository();
		// $gameRepository->addAdditionalWhere(array('column' => 'status', 'value' => Game::GAME_STATUS_ENDED, 'xxx' => '!='));
		$gameRepository->addOrderBy(array('id' => 'DESC'));
		$game = $gameRepository->getOneByRoom($roomId);
		
//		if ($gameId) {
//			$game = $gameRepository->getOneById($gameId);
//		} else {
//			
//		}
		
		$roomRepository = new RoomRepository();
		$room = $roomRepository->getOneById($roomId);
		
		$gameBox = new GameBox();
		$gameBox->setRoom($room);
		if ($game) {
			$gameBox->setGame($game);
		}
		echo $gameBox->render();
	}
}

?>