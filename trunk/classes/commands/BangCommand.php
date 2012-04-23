<?php

class BangCommand extends Command {
	const OK = 1;
	
	const NO_CARDS = 2;
	
	protected $bangCard = NULL;

	protected $attackedPlayer = NULL;

	protected $template = 'you-are-attacked.tpl';

	protected function check() {
		$attackedPlayer = $this->params[0];
		foreach ($this->players as $player) {
			$user = $player->getUser();
			if ($user['username'] == $attackedPlayer) {
				$this->attackedPlayer = $player;
				break;
			}
		}
		
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

			$this->game['inter_turn'] = $this->attackedPlayer['position'];
			$this->game['inter_turn_reason'] = serialize(array('action' => 'bang', 'from' => $this->actualPlayer['id'], 'to' => $this->attackedPlayer['id']));
			$this->game->save();

			$retval = GameUtils::throwCards($this->game, $this->actualPlayer, array($this->bangCard));

			// toto zatial nepotrebujeme
			// $this->game = $retval['game'];
			// $this->actualPlayer = $retval['player'];
		}
	}

	protected function generateMessages() {
	}

	protected function createResponse() {
		MySmarty::assign('card', $this->bangCard);
		$response = MySmarty::fetch($this->template);
		$this->attackedPlayer['command_response'] = $response;
		$this->attackedPlayer->save();

		return '';
	}
}

?>