<?php

class ChooseCardsCommand extends Command {

	const OK = 1;

	protected function check() {
		// TODO checknut pre kazde idecko ci je v poli moznych kariet, ak prislo postom, brat vsetko, tak netreba kontrolovat, hracovi dame jeho posiible karty
		$possibleChoices = unserialize($this->actualPlayer['possible_choices']);
		$possibleCards = $possibleChoices['drawn_cards'];

		$res = TRUE;
		foreach ($this->params as $param) {
			echo $param;
			if (!in_array($param, $possibleCards)) {
				$res = FALSE;
				break;
			}
		}
		if ($res === TRUE) {
			$this->check = self::OK;
		}
	}
	protected function run() {
		if ($this->check == self::OK) {
			$possibleChoices = unserialize($this->actualPlayer['possible_choices']);
			$possibleCards = $possibleChoices['drawn_cards'];

			$handCards = unserialize($this->actualPlayer['hand_cards']);
			foreach ($possibleCards as $possibleCard) {
				$handCards[] = $possibleCard;
			}

			$this->actualPlayer['hand_cards'] = serialize($handCards);
			$this->actualPlayer['command_response'] = '';
			$this->actualPlayer['possible_choices'] = '';
			$this->actualPlayer = $this->actualPlayer->save();
		}
	}

	protected function generateMessages() {
	 ;
	}
	protected function createResponse() {
	 
	}
}

?>