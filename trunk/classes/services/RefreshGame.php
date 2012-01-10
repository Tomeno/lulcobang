<?php

class RefreshGame {
	
	public function main() {
		$room = intval($_POST['room']);
		
		$gameRepository = new GameRepository();
		$game = $gameRepository->getOneByRoom($room);
		
		if ($game) {
			$GLOBALS['smarty']->assign('game', $game);
		}
		
		$loggedUser = LoggedUser::whoIsLogged();
		$GLOBALS['smarty']->assign('loggedUser', $loggedUser);
		
		echo $GLOBALS['smarty']->fetch('game.tpl');
	}
}

$service = new RefreshGame();
$service->main();

?>