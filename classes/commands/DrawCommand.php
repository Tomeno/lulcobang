<?php

class DrawCommand extends Command {
	
	const OK = 1;

	protected $template = 'cards-choice.tpl';

	protected function check() {
		// TODO dalsie podmienky - ak je serif a je uz druhe kolo musi potiahnut kartu high noon
		// ak ma dynamit, musi potiahnut kartu na dynamit
		// ak ma vazenie, takisto musi potiahnut kartu na vazenie
		
		$playerOnTurn = $this->game->getPlayerOnTurn();
		if ($playerOnTurn['id'] == $this->actualPlayer['id'] && $this->actualPlayer['phase'] == Player::PHASE_DRAW) {
			$this->check = self::OK;
		}
	}

	protected function run() {
		if ($this->check == self::OK) {
			$cards = $this->game->getDrawPile();
			$counts = $this->getCountCards();

			$drawnCards = array();
			for ($i = 0; $i < $counts['draw']; $i++) {
				$card = array_pop($cards);
				$drawnCards[] = $card['id'];
			}

			$possibleChoices = array(
				'drawn_cards' => $drawnCards,
				'possible_pick_count' => $counts['pick'],
				'rest_action' => $counts['rest_action'],
			);
			$this->actualPlayer['possible_choices'] = serialize($possibleChoices);
			$this->actualPlayer->save();

			$this->game->addAdditionalField('draw_pile', $cards);
			$drawPile = array();
			foreach ($this->game->getDrawPile() as $card) {
				$drawPile[] = $card['id'];
			}
			$this->game['draw_pile'] = serialize($drawPile);
			$this->game->save();
			
		}
	}

	private function getCountCards() {
		$counts = array(
			'draw' => 2,
			'pick' => 2,
			'rest_action' => '',
		);
		return $counts;
	}

	protected function generateMessages() {
	}

	protected function createResponse() {
		if ($this->check == self::OK) {
			$possibleChoices = unserialize($this->actualPlayer['possible_choices']);
			$cardRepository = new CardRepository();
			$possibleCards = $cardRepository->getById($possibleChoices['drawn_cards']);

			MySmarty::assign('possiblePickCount', $possibleChoices['possible_pick_count']);
			MySmarty::assign('possibleCards', $possibleCards);
			$response = MySmarty::fetch($this->template);

			$this->actualPlayer['command_response'] = $response;
			$this->actualPlayer->save();

			return $response;
		}
	}
}

?>