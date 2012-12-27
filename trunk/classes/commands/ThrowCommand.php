<?php

class ThrowCommand extends Command {

	const OK = 1;

	protected function check() {
		// TODO checker pre jose delgada?
		
		$this->check = self::OK;
	}
	
	protected function run() {
		if ($this->check == self::OK) {
			$place = $this->params[1];
			if (!$place) {
				$place = 'hand';
			}
			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, $place);
			
			if ($this->useCharacter && $this->actualPlayer->getCharacter()->getIsJoseDelgado()) {
				// TODO ulozit pocet pouziti charakteru v ramci kola - moze to spravit len dvakrat
				if ($place == 'hand' && count($this->cards) == 1 && $this->cards[0]->getIsBlue()) {
					$drawnCards = GameUtils::drawCards($this->game, 2);
					$handCards = unserialize($this->actualPlayer['hand_cards']);
					$handCards = array_merge($handCards, $drawnCards);
					$this->actualPlayer['hand_cards'] = serialize($handCards);
					
					// zistit ci sa tu nahodou nestane to ze hracovi ostanu karty v ruke a este mu pribudnu dalsie
					$this->actualPlayer->save();
				}
			}
			
			if ($place == 'table') {
				// kedze je mozne ze rusime nejaku modru kartu ktora ovplyvnuje vzdialenost, preratame maticu
				// ak to bude velmi pomale, budeme to robit len ak je medzi vyhodenymi kartami fakt takato karta
				$matrix = GameUtils::countMatrix($this->game);
				$this->game['distance_matrix'] = serialize($matrix);
				$this->game->save();
			}
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' odhodil kartu ' . $this->cards[0]->getTitle(),
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'odhodil si kartu ' . $this->cards[0]->getTitle(),
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {

	}
}

?>