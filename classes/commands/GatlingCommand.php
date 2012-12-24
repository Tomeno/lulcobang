<?php

class GatlingCommand extends Command {

	protected $template = 'you-are-attacked.tpl';

	const OK = 1;
	
	protected function check() {
		$this->check = self::OK;
	}

	protected function run() {
		if ($this->check) {
			$nextPositionPlayer = GameUtils::getPlayerOnNextPosition($this->game, $this->actualPlayer);
			
			foreach ($this->players as $player) {
				if ($player['actual_lifes'] > 0) {
					if ($player['id'] == $this->actualPlayer['id']) {
						$this->actualPlayer['phase'] = Player::PHASE_WAITING;
						$this->actualPlayer->save();
					} else {
						if ($player['id'] == $nextPositionPlayer['id']) {
							$player['phase'] = Player::PHASE_UNDER_ATTACK;
						}

						MySmarty::assign('card', $this->cards[0]);
						$response = MySmarty::fetch($this->template);
						$player['command_response'] = $response;
						$player->save();
					}
				}
			}

			$this->game['inter_turn_reason'] = serialize(array('action' => 'gatling', 'from' => $this->actualPlayer['id'], 'to' => $nextPositionPlayer['id']));
			$this->game['inter_turn'] = $nextPositionPlayer['id'];
			$this->game->save();

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil gulomet a striela na vsetkych',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si gulomet a strielas na vsetkych',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} else {
			$message = array(
				'text' => 'nieco sa stalo pri pouziti gulometu',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
	}
}

?>