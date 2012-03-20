<?php

class ChooseCardsCommand extends Command {

	const OK = 1;

	const TOO_MANY_CARDS = 2;

	const NOT_ENOUGH_CARDS = 3;

	const NOT_POSSIBLE_CARD_CHOOSED = 4;

	protected function check() {

		$possibleChoices = unserialize($this->actualPlayer['possible_choices']);
		$possibleCards = $possibleChoices['drawn_cards'];
		$possiblePickCount = $possibleChoices['possible_pick_count'];

		$res = TRUE;
		if ($possiblePickCount == count($this->params)) {
			foreach ($this->params as $param) {
				if (!in_array($param, $possibleCards)) {
					$res = FALSE;
					break;
				}
			}
		
			if ($res === TRUE) {
				$this->check = self::OK;
			} else {
				$this->check = self::NOT_POSSIBLE_CARD_CHOOSED;
			}
		} elseif ($possiblePickCount < count($this->params)) {
			$this->check = self::TOO_MANY_CARDS;
		} else {
			$this->check = self::NOT_ENOUGH_CARDS;
		}
	}
	
	protected function run() {
		if ($this->check == self::OK) {
			$possibleChoices = unserialize($this->actualPlayer['possible_choices']);
			$possibleCards = $possibleChoices['drawn_cards'];
			$restAction = $possibleChoices['rest_action'];

			$handCards = unserialize($this->actualPlayer['hand_cards']);
			$drawPile = unserialize($this->game['draw_pile']);
			$throwPile = unserialize($this->game['throw_pile']);
			
			foreach ($possibleCards as $possibleCard) {
				if (in_array($possibleCard, $this->params)) {
					$handCards[] = $possibleCard;
				} else {
					if ($restAction == 'back_to_deck') {
						$drawPile[] = $possibleCard;
					}
				}
			}

			$this->actualPlayer['phase'] = Player::PHASE_PLAY;
			$this->actualPlayer['hand_cards'] = serialize($handCards);
			$this->actualPlayer['command_response'] = '';
			$this->actualPlayer['possible_choices'] = '';
			$this->actualPlayer = $this->actualPlayer->save(TRUE);

			$this->game['draw_pile'] = serialize($drawPile);
			$this->game['throw_pile'] = serialize($throwPile);
			$this->game = $this->game->save(TRUE);
		}
	}

	protected function generateMessages() {
	 ;
	}
	protected function createResponse() {
	 
	}
}

?>