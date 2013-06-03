<?php

abstract class AbstractStrategy {
	
	/**
	 * player
	 * 
	 * @var Player
	 */
	protected $player = NULL;
	
	/**
	 * game
	 * 
	 * @var	Game
	 */
	protected $game = NULL;
	
	public function __construct(Player $player, Game $game) {
		$this->player = $player;
		$this->game = $game;
	}
	
	public function play() {
		$command = '';
		
		if ($this->game['status'] == Game::GAME_STATUS_INITIALIZED) {
			$command = $this->selectCharacter();
		} elseif ($this->player['phase'] == Player::PHASE_DRAW_HIGH_NOON) {
			$command = $this->drawOneTurnCard();
		} elseif ($this->player['phase'] == Player::PHASE_DYNAMITE) {
			$command = $this->drawDynamite();
		} elseif ($this->player['phase'] == Player::PHASE_JAIL) {
			$command = $this->drawJail();
		} elseif ($this->player['phase'] == Player::PHASE_RATTLESNAKE) {
			$command = $this->drawRattlesnake();
		} elseif ($this->player['phase'] == Player::PHASE_DRAW) {
			$command = $this->draw();
		} elseif ($this->player['phase'] == Player::PHASE_PLAY) {
			if ($this->player['possible_choices'] != '') {
				$command = $this->chooseCards();
			} else {
				$command = $this->playCards();
			}
		} elseif ($this->player['phase'] == Player::PHASE_UNDER_ATTACK) {
			$command = $this->reactToAttack();
		} elseif ($this->player['phase'] == Player::PHASE_HIGH_NOON) {
			$command = $this->takeLifeDown();
		} else {
			$this->whatToDo();
		}
		
		if ($command) {
			Log::logAiAction('ai player ' . $this->player['id'] . ': ' . $command);
			Command::setup($command, $this->game, $this->player);
		}
	}
	
	protected function selectCharacter() {
		// only select random character for now
		$possibleChoices = unserialize($this->player['possible_choices']);
		$possibleCharacters = $possibleChoices['possible_characters'];
		$character = $possibleCharacters[array_rand($possibleCharacters)];
		
		return 'command=choose_character&selectedCharacter=' . $character;
	}

	/**
	 * TODO use character
	 * @return string
	 */
	
	protected function drawJail() {
		return 'command=draw&playCardName=jail';
	}
	
	protected function drawRattlesnake() {
		return 'command=draw&playCardName=rattlesnake';
	}
	
	protected function drawDynamite() {
		return 'command=draw&playCardName=dynamite';
	}

	protected function drawOneTurnCard() {
		return 'command=draw_high_noon';
	}
	
	protected function draw() {
		$command = 'command=draw';
		if ($this->game->getIsHNPeyote()) {
			$colors = array('red', 'black');
			$color = $colors[mt_rand(0, 1)];
			$command .= '&peyoteColor=' . $color;
		}
		return $command;
	}
	
	protected function chooseCards() {
		$possibleChoices = unserialize($this->player['possible_choices']);
		$selectedCards = $possibleChoices['drawn_cards'];
		return 'command=choose_cards&selectedCards=' . implode(',', $selectedCards);
	}
	
	abstract protected function playCards();
	
	protected function reactToAttack() {
		$interTurnReason = unserialize($this->game['inter_turn_reason']);
		if ($interTurnReason['action'] == 'bang') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'gatling') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'howitzer') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'pepperbox') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'buffalorifle') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'derringer') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'knife') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'punch') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'springfield') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'doc_holyday') {
			$command = $this->reactToBangLikeAttack();	
		} elseif ($interTurnReason['action'] == 'duel') {
			$command = $this->reactToDuel();
		} elseif ($interTurnReason['action'] == 'indians') {
			$command = $this->reactToIndians();
		} elseif ($interTurnReason['action'] == 'general_store') {
			$command = $this->reactGeneralStore();
		} elseif ($interTurnReason['action'] == 'tomahawk') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'aiming') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'backfire') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'fanning') {
			$command = $this->reactToBangLikeAttack();
		} elseif ($interTurnReason['action'] == 'poker') {
			$command = $this->reactPoker();
		} elseif ($interTurnReason['action'] == 'wild_band') {
			$command = $this->reactWildBand();
		} else {
			$this->whatToDo();
		}

		if ($command) {
			return $command;
		}
	}
	
	protected function drawExtraCards() {
		$command = '';
		if ($this->player->getHasPonyExpressOnTheTable()) {
			$ponyExpress = $this->player->getHasPonyExpressOnTheTable();
			$command = 'command=ponyexpress&playCardId=' . $ponyExpress['id'] . '&playCardName=' . $ponyExpress->getCardName();
		} elseif ($this->player->getHasWellsFargoOnHand()) {
			$wellsFargo = $this->player->getHasWellsFargoOnHand();
			$command = 'command=wellsfargo&playCardId=' . $wellsFargo['id'] . '&playCardName=' . $wellsFargo->getCardName();
		} elseif ($this->player->getHasDiligenzaOnHand()) {
			$diligenza = $this->player->getHasDiligenzaOnHand();
			$command = 'command=diligenza&playCardId=' . $diligenza['id'] . '&playCardName=' . $diligenza->getCardName();
		}
		return $command;
	}
	
	protected function putCardsToTheTable() {
		$command = '';
		$handCards = $this->player->getHandCards();
		foreach ($handCards as $handCard) {
			if ($handCard->getIsGreen() || $handCard->getIsBlue()) {
				$cardName = $handCard->getCardName();
				$methodOnTheTable = 'getHas' . ucfirst($cardName) . 'OnTheTable';
				$methodOnWait = 'getHas' . ucfirst($cardName) . 'OnWait';
				if (!$this->player->$methodOnTheTable() && !$this->player->$methodOnWait()) {
					// zatial nevykladam zbrane a nebudem sam seba davat do vazenia :)
					if (!$handCard->getIsWeapon() && !$handCard->getIsJail()) {
						$command = 'command=put&playCardId=' . $handCard['id'] . '&playCardName=' . $cardName;
					}
				}
			}
		}
		return $command;
	}
	
	protected function addLives() {
		$command = '';
		if (($this->player['max_lifes'] - $this->player['actual_lifes']) >= 1) {
			$handCards = $this->player->getHandCards();
			$whisky = $this->player->getHasWhiskyOnHand();
			if (($this->player['max_lifes'] - $this->player['actual_lifes']) >= 2 && $whisky && count($handCards) >= 2) {
				// TODO pri hre dvoch hracov je najlepsie pouzit beer ako additional card
				$additionalCard = $handCards[array_rand($handCards)];
				$command .= 'command=whisky&playCardId=' . $whisky['id'] . '&playCardName=' . $whisky->getCardName();
				$command .= '&additionalCardsId=' . $additionalCard['id'] . '&additionalCardsName=' . $additionalCard->getCardName();
			} else {
				$canteen = $this->player->getHasCanteenOnTheTable();
				if ($canteen) {
					$command = 'command=canteen&playCardId=' . $canteen['id'] . '&playCardName=' . $canteen->getCardName();
				} else {
					$tequila = $this->player->getHasTequilaOnHand();
					if ($tequila) {
						// TODO pri hre dvoch hracov je najlepsie pouzit beer ako additional card
						$additionalCard = $handCards[array_rand($handCards)];
						$command .= 'command=tequila&playCardId=' . $tequila['id'] . '&playCardName=' . $tequila->getCardName();
						$command .= '&additionalCardsId=' . $additionalCard['id'] . '&additionalCardsName=' . $additionalCard->getCardName();
					} else {
						if (!$this->game->getIsHNTheReverend()) {
							$beer = $this->player->getHasBeerOnHand();
							$alivePlayers = $this->game->getAlivePlayers();
							if (count($alivePlayers) > 2 && $beer) {
								$command = 'command=beer&playCardId=' . $beer['id'] . '&playCardName=' . $beer->getCardName();
							}
						}
					}
				}
			}
		}
		return $command;
	}
	
	protected function throwExtraCards() {
		$command = '';
		$handCards = $this->player->getHandCards();
		$handCardsCount = count($handCards);
		
		if (($this->player->getIsSeanMallory($this->game) && $handCardsCount > 10) ||
			(!$this->player->getIsSeanMallory($this->game) && $this->player['actual_lifes'] < $handCardsCount)) {
			$thrownCard = $handCards[array_rand($handCards)];
			$command = 'command=throw&playCardId=' . $thrownCard['id'] . '&playCardName=' . $thrownCard->getCardName();
		}
		return $command;
	}
	
	protected function playBarrelMissedCardsOrTakeLifeDown() {
		$interTurnReason = unserialize($this->game['inter_turn_reason']);
		$belleStar = FALSE;
		$useCard = NULL;
		if (isset($interTurnReason['from'])) {
			$attackingPlayerId = $interTurnReason['from'];
			$playerRepository = new PlayerRepository();
			$attackingPlayer = $playerRepository->getOneById($attackingPlayerId);
			if ($attackingPlayer->getIsBelleStar($this->game)) {
				$belleStar = TRUE;
			}
		}
		
		if ($belleStar === FALSE) {
			// proti belle star nemozu hraci pouzivat karty, ktore su na stole
			$barrel = $this->player->getHasBarrelOnTheTable();
			$notices = $this->player->getNoticeList();

			if ((!isset($notices['barrel_used']) || $notices['barrel_used'] != 1) && $barrel) {
				return 'command=draw&playCardName=barrel';
			}

			$greenMissedCards = $this->player->getMissedCardOnTheTable();

			$maxCardsCountEffect = -10;
			foreach ($greenMissedCards as $greenMissedCard) {
				$cardsCountEffect = $greenMissedCard->getCardsCountEffect();
				if ($cardsCountEffect > $maxCardsCountEffect) {
					$useCard = $greenMissedCard;
				}
			}
		}
		if ($useCard === NULL) {
			$dodge = $this->player->getHasDodgeOnHand();
			if ($dodge) {
				$useCard = $dodge;
			} else {
				$missed = $this->player->getHasMissedOnHand();
				if ($missed) {
					$useCard = $missed;
				}
			}
		}
		
		if ($useCard) {
			return 'command=' . $useCard->getCardName() . '&playCardId=' . $useCard['id'] . '&playCardName=' . $useCard->getCardName();
		} else {
			return $this->takeLifeDown();
		}
	}
	
	protected function playBangAsDefensiveCardOrTakeLifeDown() {
		$bang = $this->player->getHasBangOnHand();
		if ($bang) {
			return 'command=bang&playCardId=' . $bang['id'] . '&playCardName=' . $bang->getCardName();
		} else {
			return $this->takeLifeDown();
		}
	}
	
	protected function reactToBangLikeAttack() {
		return $this->playBarrelMissedCardsOrTakeLifeDown();
	}
	
	protected function reactToDuel() {
		return $this->playBangAsDefensiveCardOrTakeLifeDown();
	}

	protected function reactToIndians() {
		return $this->playBangAsDefensiveCardOrTakeLifeDown();
	}
	
	protected function reactGeneralStore() {
		$possibleChoices = unserialize($this->player['possible_choices']);
		$possibleCards = $possibleChoices['drawn_cards'];
		return 'command=choose_cards&selectedCards=' . $possibleCards[array_rand($possibleCards)];
	}
	
	protected function takeLifeDown() {
		$command = 'command=life';
		$beer = NULL;
		if ($this->player['actual_lifes'] == 1) {
			$beer = $this->player->getHasBeerOnHand();
			if ($beer) {
				$command .= '&playCardId=' . $beer['id'] . '&playCardName=' . $beer->getCardName();
			}
		}
		
		$interTurnReason = unserialize($this->game['inter_turn_reason']);
		if (isset($interTurnReason['from'])) {
			$attackingPlayerId = $interTurnReason['from'];
			$playerRepository = new PlayerRepository();
			$attackingPlayer = $playerRepository->getOneById($attackingPlayerId);

			if ($attackingPlayer && $attackingPlayer->getHasShootgunOnTheTable()) {
				$handCards = $this->player->getHandCards();
				if ($handCards) {
					$thrownCard = $handCards[array_rand($handCards)];
					if ($beer) {
						$command .= '&additionalCardsId=' . $thrownCard['id'] . '&additionalCardsName=' . $thrownCard->getCardName();
					} else {
						$command .= '&playCardId=' . $thrownCard['id'] . '&playCardName=' . $thrownCard->getCardName();
					}
				}
			}
		}
		
		return $command;
	}
	
	protected function reactPoker() {
		$command = '';
		$handCards = $this->player->getHandCards();
		$thrownCard = $handCards[array_rand($handCards)];
		$command = 'command=throw&playCardId=' . $thrownCard['id'] . '&playCardName=' . $thrownCard->getCardName();
		return $command;
	}
	
	protected function reactWildBand() {
		$bang = $this->player->getHasBangOnHand();
		if ($bang) {
			return 'command=bang&playCardId=' . $bang['id'] . '&playCardName=' . $bang->getCardName();
		} else {
			$handCards = $this->player->getHandCards();
			if (count($handCards) >= 2) {
				shuffle($handCards);
				$command = 'command=throw';
				for ($i = 0; $i < 2; $i++) {
					$card = $handCards[$i];
					if ($i == 0) {
						$command .= '&playCardId=' . $card['id'] . '&playCardName=' . $card->getCardName();
					} elseif ($i == 1) {
						$command .= '&additionalCardsId=' . $card['id'] . '&additionalCardsName=' . $card->getCardName();
					}
				}
				return $command;
			} else {
				// ma si hrac zobrat zivot ked nema karty? alebo vobec nema byt cielom karty wild band?
				return $this->takeLifeDown();
			}
		}
	}
	
	protected function findPossibleTargets($distance = 0) {
		return $this->player->findTargetsInDistance($this->game, $distance);
	}
	
	protected function whatToDo() {
		$message = 'hrac ' . $this->player['id'] . ' je vo faze ' . $this->player['phase'];
		$message .= ' a mal by ist ale nevie co ma spravit';
		if ($this->game['inter_turn_reason']) {
			$message .= ' lebo hra je vo faze ';
			$message .= print_R($this->game['inter_turn_reason'], TRUE);
		}
		Log::logAiAction($message);
	}
}

?>