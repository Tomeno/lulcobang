<?php

class BangCommand extends Command {
	const OK = 1;
	
	const NO_CARDS = 2;
	
	protected $bangCard = NULL;

	protected $attackedPlayer = NULL;

	protected $template = 'you-are-attacked.tpl';

	protected function check() {

		// TODO check actual player state - if waiting cannot play bang again

		// TODO check if has volcanic or is willy the kid for playing more than one bang in a round

		// TODO create as checker
		$attackedPlayer = $this->params[0];
		foreach ($this->players as $player) {
			$user = $player->getUser();
			if ($user['username'] == $attackedPlayer) {
				$this->attackedPlayer = $player;
				break;
			}
		}
		
		// TODO replace bangCard by $this->card (from checker)

		$method = 'getHasBangOnHand';
		$res = $this->actualPlayer->$method();
		if ($res) {
			$this->bangCard = $res;
		}
		
		if ($this->bangCard !== NULL && $this->attackedPlayer !== NULL) {
			$this->check = self::OK;
		}

	}

	protected function run() {
		if ($this->check == self::OK) {

			$this->attackedPlayer['phase'] = Player::PHASE_UNDER_ATTACK;
			$this->attackedPlayer->save(TRUE);

			$this->actualPlayer['phase'] = Player::PHASE_WAITING;
			$this->actualPlayer->save();

			// TODO toto plati len ak je to utok bangom, ale bang sa pouziva na viacerych miestach - premysliet a dorobit aj duel a indianov prip dalsie
			$this->game['inter_turn'] = $this->attackedPlayer['position'];
			$this->game['inter_turn_reason'] = serialize(array('action' => 'bang', 'from' => $this->actualPlayer['id'], 'to' => $this->attackedPlayer['id']));
			$this->game->save();

			// TODO nastavit ze hrac pouzil bang ak sa jedna o jeho utok na niekoho pomocou bangu

			$retval = GameUtils::throwCards($this->game, $this->actualPlayer, array($this->bangCard));
		}
	}

	protected function generateMessages() {
	}

	protected function createResponse() {
		if ($this->check == self::OK) {
			// TODO prerobit tak aby to fungovalo aj bez javascriptu, onclick treba nahradit niecim inym, pripadne doplnit tlacitko ktore skryje ten overlay

			MySmarty::assign('card', $this->bangCard);
			$response = MySmarty::fetch($this->template);
			$this->attackedPlayer['command_response'] = $response;
			$this->attackedPlayer->save();
		}

		return '';
	}
}

?>