<?php

class ExecuteCommand {
	public function main() {
		$gameId = intval(Utils::post('game'));
		$playCardId = intval(Utils::post('playCard'));
		$additionalCardId = intval(Utils::post('additionalCard'));
		$cardId = intval(Utils::post('card'));
		$playerId = intval(Utils::post('player'));
		$command = addslashes(Utils::post('command'));
		$place = addslashes(Utils::post('place'));
		$useCharacter = intval(Utils::post('useCharacter'));

		$gameRepository = new GameRepository();
		$game = $gameRepository->getOneById($gameId);
		
		$commandString = '';
		if ($useCharacter == 1) {
			$commandString .= 'char ';
		}
		
		$commandString .= $command;
		if ($playerId) {
			$playerRepository = new PlayerRepository();
			$player = $playerRepository->getOneById($playerId);
			if ($player) {
				$user = $player->getUser();
				if ($user) {
					$commandString .= ' ' . $user['username'];
				}
			}
		}

		if ($playCardId) {
			if (!$cardId && !$playerId) {
				$cardRepository = new CardRepository(TRUE);
				$card = $cardRepository->getOneById($playCardId);
				$commandString .= ' ' . str_replace('-', '', $card->getItemAlias());
			}
		}
		
		if ($additionalCardId) {
			//if (!$cardId && !$playerId) {
				$cardRepository = new CardRepository(TRUE);
				$card = $cardRepository->getOneById($additionalCardId);
				$commandString .= ' ' . str_replace('-', '', $card->getItemAlias());
			//}
		}
		
		if ($cardId) {
			$cardRepository = new CardRepository(TRUE);
			$card = $cardRepository->getOneById($cardId);
			$commandString .= ' ' . str_replace('-', '', $card->getItemAlias());
		}

		if ($place) {
			$commandString .= ' ' . $place;
		}
		//echo $commandString;
		//exit();
		Command::setup($commandString, $game);
	}
}

?>