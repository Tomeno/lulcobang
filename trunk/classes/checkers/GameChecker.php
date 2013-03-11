<?php

/**
 * class for checking the game
 *
 * @author	Michal Lulco <michal.lulco@gmail.com>
 */
class GameChecker extends Checker {

	/**
	 * main check method
	 *
	 * @return	boolean
	 */
	public function check() {
		if ($this->precheckerParams && is_array($this->precheckerParams)) {
			$precheck = TRUE;
			foreach ($this->precheckerParams as $precheckMethod) {
				$precheck = $this->$precheckMethod();
				if ($precheck === FALSE) {
					break;
				}
				return $precheck;
			}
		} else {
			$message = array(
				'localizeKey' => 'missing_checker_params',
			);

			$this->addMessage($message);
			return FALSE;
		}
	}

	/**
	 * checks if it is true that no game in room exists
	 *
	 * @return	boolean
	 */
	protected function noGameExists() {
		$game = $this->command->getGame();
		if ($game === NULL || $game['status'] == Game::GAME_STATUS_ENDED) {
			return TRUE;
		} else {
			$message = array(
				'localizeKey' => 'game_already_created',
			);
			$this->addMessage($message);
			return FALSE;
		}
	}

	/**
	 * checks if it is true that some game in room exists
	 *
	 * @return	boolean
	 */
	protected function gameExists() {
		$game = $this->command->getGame();
		if ($game === NULL || $game['status'] == Game::GAME_STATUS_ENDED) {
			$message = array(
				'localizeKey' => 'no_game_exists',
			);
			$this->addMessage($message);
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * checks if the game in room is initialized
	 *
	 * @note	checks if game exists by method gameExists() first
	 * @return	boolean
	 */
	protected function gameInitialized() {
		if ($this->gameExists()) {
			$game = $this->command->getGame();
			if ($game['status'] == Game::GAME_STATUS_INITIALIZED) {
				return TRUE;
			} else {
				$message = array(
					'localizeKey' => 'game_not_yet_initialized',
				);
				$this->addMessage($message);
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * cheks if the game in room is started
	 *
	 * @note	checks if game exists by method gameExists() first
	 * @return	boolean
	 */
	protected function gameStarted() {
		if ($this->gameExists()) {
			$game = $this->command->getGame();
			if ($game['status'] == Game::GAME_STATUS_STARTED) {
				return TRUE;
			} else {
				$message = array(
					'localizeKey' => 'game_not_yet_started',
				);
				$this->addMessage($message);
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
}

?>