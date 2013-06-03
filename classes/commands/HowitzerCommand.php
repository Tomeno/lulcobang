<?php

class HowitzerCommand extends Command {

	protected $template = 'you-are-attacked.tpl';

	const OK = 1;
	
	protected function check() {
		$this->check = self::OK;
	}

	protected function run() {
		if ($this->check == self::OK) {
			$nextPositionPlayer = GameUtils::getPlayerOnNextPosition($this->game, $this->actualPlayer);
			
			foreach ($this->players as $player) {
				if ($player->getIsAlive()) {
					if ($player['id'] == $this->actualPlayer['id']) {
						$this->actualPlayer['phase'] = Player::PHASE_WAITING;
						
						if ($this->actualPlayer->getIsBelleStar($this->game)) {
							$notices = $this->actualPlayer->getNoticeList();
							$notices['character_used'] = 1;
							$this->actualPlayer->setNoticeList($notices);
						}
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

			$this->game['inter_turn_reason'] = serialize(array('action' => 'howitzer', 'from' => $this->actualPlayer['id'], 'to' => $nextPositionPlayer['id']));
			$this->game['inter_turn'] = $nextPositionPlayer['id'];
			$this->game->save();

			GameUtils::throwCards($this->game, $this->actualPlayer, $this->cards, 'table');
		}
	}

	protected function generateMessages() {
		if ($this->check == self::OK) {
			$message = array(
				'text' => $this->loggedUser['username'] . ' pouzil hufnicu a striela na vsetkych',
				'notToUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
			
			$message = array(
				'text' => 'pouzil si hufnicu a strielas na vsetkych',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		} else {
			$message = array(
				'text' => 'nieco sa stalo pri pouziti hufnice',
				'toUser' => $this->loggedUser['id'],
			);
			$this->addMessage($message);
		}
	}

	protected function createResponse() {
	}
}

?>