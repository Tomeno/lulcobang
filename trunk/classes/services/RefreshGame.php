<?php

class RefreshGame {
	
	public function main() {
		$gameId = intval(Utils::post('game'));
		
		$gameRepository = new GameRepository();
		$game = $gameRepository->getOneById($gameId);
		
		if ($game) {
			$gameBox = new GameBox();
			$gameBox->setGame($game);
			echo $gameBox->render();
		}
	}
}

?>