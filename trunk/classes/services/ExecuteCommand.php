<?php

class ExecuteCommand {
	public function main() {
		$gameId = intval(Utils::post('game'));
		$gameRepository = new GameRepository();
		$game = $gameRepository->getOneById($gameId);
		
		$commandParams = array();
		$commandParams['command'] = addslashes(Utils::post('command'));
		$commandParams['useCharacter'] = intval(Utils::post('useCharacter'));
		$commandParams['playCardId'] = intval(Utils::post('playCard'));
		if ($commandParams['playCardId']) {
			$cardRepository = new CardRepository(TRUE);
			$card = $cardRepository->getOneById($commandParams['playCardId']);
			$commandParams['playCardName'] = str_replace('-', '', $card->getItemAlias());
		}
		// TODO mozno ich niekedy bude viac
		$commandParams['additionalCardsId'] = intval(Utils::post('additionalCard'));
		if ($commandParams['additionalCardsId']) {
			$cardRepository = new CardRepository(TRUE);
			$card = $cardRepository->getOneById($commandParams['additionalCardsId']);
			$commandParams['additionalCardsName'] = str_replace('-', '', $card->getItemAlias());
		}

		$commandParams['enemyPlayerId'] = intval(Utils::post('player'));
		if ($commandParams['enemyPlayerId']) {
			$playerRepository = new PlayerRepository();
			$player = $playerRepository->getOneById($commandParams['enemyPlayerId']);
			if ($player) {
				$user = $player->getUser();
				if ($user) {
					$commandParams['enemyPlayerUsername'] = $user['username'];
				}
			}
		}
		
		if ($commandParams['command'] == 'fanning') {
			$commandParams['additionalEnemyPlayerId'] = intval(Utils::post('additionalPlayer'));
			if ($commandParams['additionalEnemyPlayerId']) {
				$playerRepository = new PlayerRepository();
				$player = $playerRepository->getOneById($commandParams['additionalEnemyPlayerId']);
				if ($player) {
					$user = $player->getUser();
					if ($user) {
						$commandParams['additionalEnemyPlayerUsername'] = $user['username'];
					}
				}
			}
		}
		
		// TODO brawl tu dava addslashes
		if ($commandParams['command'] == 'brawl') {
			$commandParams['enemyCardsId'] = addslashes(Utils::post('card'));
		} else {
			$commandParams['enemyCardsId'] = intval(Utils::post('card'));
			if ($commandParams['enemyCardsId']) {
				$cardRepository = new CardRepository(TRUE);
				$card = $cardRepository->getOneById($commandParams['enemyCardsId']);
				$commandParams['enemyCardsName'] = str_replace('-', '', $card->getItemAlias());
			}
		}
		$commandParams['place'] = addslashes(Utils::post('place'));
		
		if (Utils::post('peyoteColor')) {
			$commandParams['peyoteColor'] = addslashes(Utils::post('peyoteColor'));
		}

		if (Utils::post('text')) {
			$commandParams['text'] = addslashes(Utils::post('text'));
		}
		
		$params = array();
		foreach ($commandParams as $key => $value) {
			$params[] = $key . '=' . $value;
		}
		
		$commandString = implode('&', $params);
		Log::command($commandString);
		Command::setup($commandString, $game);
	}
}

?>