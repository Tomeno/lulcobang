<?php

class EnemyPlayerHasCardsChecker extends Checker {

	protected $cards = array();

	protected $place = 'hand';

	public function setCards(array $cards) {
		$this->cards = $cards;
	}

	public function getPlace() {
		return $this->place;
	}

	public function check() {
		if ($this->precheckerParams && is_array($this->precheckerParams)) {
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

	protected function hasAllCardsOnTheTable() {
		$cards = array();
		foreach ($this->cards as $card) {
			$methodName = 'getHas' . ucfirst($card) . 'OnTheTable';
			$card = $this->command->getEnemyPlayer()->$methodName();
			if (!$card) {
				return array();
			}
			$cards[] = $card;
		}
		return $cards;
	}

	protected function hasAllCardsOnWait() {
		$cards = array();
		foreach ($this->cards as $card) {
			$methodName = 'getHas' . ucfirst($card) . 'OnWait';
			$card = $this->command->getEnemyPlayer()->$methodName();
			if (!$card) {
				return array();
			}
			$cards[] = $card;
		}
		return $cards;
	}

	protected function hasAllCardsOnTheTableOrOnWait() {
		// TODO add messages
		// TODO add parameter to upper two methods for writing partial messages or not
		$onTheTableCards = $this->hasAllCardsOnTheTable();
		if (empty($onTheTableCards)) {
			$onWaitCards = $this->hasAllCardsOnWait();
			if (!empty($onWaitCards)) {
				$this->command->addEnemyPlayerCards($this->command->getEnemyPlayer(), $onWaitCards);
				$this->place = 'wait';
				return TRUE;
			}
		} else {
			$this->command->addEnemyPlayerCards($this->command->getEnemyPlayer(), $onTheTableCards);
			$this->place = 'table';
			return TRUE;
		}
		return FALSE;
	}
}

?>