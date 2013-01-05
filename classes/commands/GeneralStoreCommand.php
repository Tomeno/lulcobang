<?php

class GeneralStoreCommand extends Command {
	
	const OK = 1;

	protected $template = 'cards-choice.tpl';
	
	protected $drawnCards = array();
	
	protected function check() {
		$this->check = self::OK;
	}

	protected function run() {
		if ($this->check == self::OK) {
			$livePlayers = 0;
			foreach ($this->getPlayers() as $player) {
				if ($player['actual_lifes'] > 0) {
					$livePlayers++;
				}
			}
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
			$this->drawnCards = GameUtils::drawCards($this->game, $livePlayers);
			
			$this->game['inter_turn_reason'] = serialize(array('action' => 'general_store', 'from' => $this->actualPlayer['id'], 'cards' => $this->getCardIds()));
			$this->game['inter_turn'] = $this->actualPlayer['id'];
			$this->game->save();
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => 'pouzil si kartu obchod',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil kartu obchod',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
		$cardRepository = new CardRepository();
		$possibleCards = array();
		foreach ($this->drawnCards as $cardId) {
			$possibleCards[] = $cardRepository->getOneById($cardId);
		}

		MySmarty::assign('possiblePickCount', 1);
		MySmarty::assign('possibleCards', $possibleCards);
		MySmarty::assign('possibleCardsCount', count($possibleCards));
		MySmarty::assign('game', $this->game);
		$response = MySmarty::fetch($this->template);

		$playerPossibleChoices = array(
			'drawn_cards' => $this->drawnCards,
			'possible_pick_count' => 1,
			'rest_action' => 'general_store',
		);
		$this->actualPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
		$this->actualPlayer['possible_choices'] = serialize($playerPossibleChoices);
		$this->actualPlayer['command_response'] = $response;
		$this->actualPlayer->save();

		return $response;
	}
}

?>