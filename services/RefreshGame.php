<?php

require_once('../include.php');

class RefreshGame {
	
	public function main() {
		$room = intval($_POST['room']);
		
		$gameRepository = new GameRepository();
		$game = $gameRepository->getOneByRoom($room);
		
		if ($game) {
			$GLOBALS['smarty']->assign('game', $game);
		}
		
		$loggedUser = User::whoIsLogged();
		$GLOBALS['smarty']->assign('loggedUser', $loggedUser);
		
		echo $GLOBALS['smarty']->fetch('game.tpl');
	}
}

$service = new RefreshGame();
$service->main();

?>