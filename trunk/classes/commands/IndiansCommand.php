<?php

class IndiansCommand extends Command {

	protected $template = 'you-are-attacked.tpl';

	protected function check() {
	}

	protected function run() {
		$nextPosition = GameUtils::getNextPosition($this->game);
		
		foreach ($this->players as $player) {
			if ($player['id'] == $this->actualPlayer['id']) {
				$this->actualPlayer['phase'] = Player::PHASE_WAITING;
				$this->actualPlayer->save();
			} else {
				if ($player['position'] == $nextPosition) {
					$nextPositionPlayer = $player;
					$player['phase'] = Player::PHASE_UNDER_ATTACK;
				}

				MySmarty::assign('card', $this->cards[0]);
				$response = MySmarty::fetch($this->template);
				$player['command_response'] = $response;
				$player->save();
			}
		}

		$this->game['inter_turn_reason'] = serialize(array('action' => 'indians', 'from' => $this->actualPlayer['id'], 'to' => $nextPositionPlayer['id']));
		$this->game['inter_turn'] = $nextPosition;
		$this->game->save();

		GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards);
	}

	protected function generateMessages() {
	}

	protected function createResponse() {
	}
}

?>