<?php

class ChooseCardsCommand extends Command {

	const OK = 1;

	const TOO_MANY_CARDS = 2;

	const NOT_ENOUGH_CARDS = 3;

	const NOT_POSSIBLE_CARD_CHOOSED = 4;

	protected $template = 'cards-choice.tpl';
	
	protected function check() {
		$possibleChoices = unserialize($this->actualPlayer['possible_choices']);
		$possibleCards = $possibleChoices['drawn_cards'];
		$possiblePickCount = $possibleChoices['possible_pick_count'];

		$this->params['selectedCards'] = explode(',', $this->params['selectedCards']);
		
		$res = TRUE;
		if ($possiblePickCount == count($this->params['selectedCards'])) {
			foreach ($this->params['selectedCards'] as $param) {
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
		} elseif ($possiblePickCount < count($this->params['selectedCards'])) {
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
			$nextPlayerPossibleCards = array();
			
			foreach ($possibleCards as $possibleCard) {
				if (in_array($possibleCard, $this->params['selectedCards'])) {
					$handCards[] = $possibleCard;
				} else {
					if ($restAction == 'back_to_deck') {
						$drawPile[] = $possibleCard;
					} elseif ($restAction == 'general_store') {
						$nextPlayerPossibleCards[] = $possibleCard;
					}
				}
			}

			if ($this->interTurnReason['action'] == 'general_store') {
				$nextPositionPlayer = GameUtils::getPlayerOnNextPosition($this->game, $this->actualPlayer);
				if ($nextPositionPlayer['id'] == $this->attackingPlayer['id']) {
					$this->game['inter_turn_reason'] = '';
					$this->game['inter_turn'] = 0;
					
					$this->attackingPlayer['phase'] = Player::PHASE_PLAY;
					$this->attackingPlayer->save();
				} else {
					if ($this->actualPlayer['id'] == $this->attackingPlayer['id']) {
						$this->actualPlayer['phase'] = Player::PHASE_WAITING;
					} else {
						$this->actualPlayer['phase'] = Player::PHASE_NONE;
					}
					$this->game['inter_turn'] = $nextPositionPlayer['id'];
					
					$cardRepository = new CardRepository();
					$possibleCards = array();
					foreach ($nextPlayerPossibleCards as $cardId) {
						$possibleCards[] = $cardRepository->getOneById($cardId);
					}

					MySmarty::assign('possiblePickCount', 1);
					MySmarty::assign('possibleCards', $possibleCards);
					MySmarty::assign('possibleCardsCount', count($possibleCards));
					MySmarty::assign('game', $this->game);
					$response = MySmarty::fetch($this->template);

					$playerPossibleChoices = array(
						'drawn_cards' => $nextPlayerPossibleCards,
						'possible_pick_count' => 1,
						'rest_action' => 'general_store',
					);
					$nextPositionPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
					$nextPositionPlayer['possible_choices'] = serialize($playerPossibleChoices);
					$nextPositionPlayer['command_response'] = $response;
					$nextPositionPlayer->save();
				}
			} else {
				$this->actualPlayer['phase'] = Player::PHASE_PLAY;
			}
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
		
	}
	protected function createResponse() {
	 
	}
}

?>