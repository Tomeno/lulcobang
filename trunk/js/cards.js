function selectCardToPlay(id, type, place) {
	var selectedPlayCard = $('selected-play-card');
	var selectedAdditionalCard = $('selected-additional-card');
	var actualCard = $('card-' + id);
	if (selectedPlayCard.value != 0) {
		if (selectedPlayCard.value == id) {
			selectedPlayCard.value = 0;
			rejectCard(actualCard, 'main');
			$('command').value = '';
			$('place').value = '';
		} else {
			if (selectedAdditionalCard.value == id) {
				selectedAdditionalCard.value = 0;
				rejectCard(actualCard, 'additional');
			} else {
				actualSelectedAdditionalCard = $('card-' + selectedAdditionalCard.value);
				if (actualSelectedAdditionalCard) {
					rejectCard(actualSelectedAdditionalCard, 'additional');
				}
				
				selectedAdditionalCard.value = id;
				ejectCard(actualCard, 'additional');
			}
		}
	} else {
		selectedPlayCard.value = id;
		ejectCard(actualCard, 'main');
		if (type) {
			type = type.replace(/-/g, '');
			$('command').value = type;
			
			var table = $('table');
			if (table) {
				table.setAttribute('class', type);
			}
		}
		
		if (place) {
			$('place').value = place;
//			alert(place);
		} else {
			$('place').value = '';
		}
	}
	
	var command = $('command').value;
	var phase = $('phase').value;
	var characterName = $('character-name').value;
//	alert(command);
//	alert(phase);

	if (characterName) {
		if (characterName == 'uncle-will') {
			executeCommand();
		} else {
			if (characterName == 'calamity-janet' && phase == 'under_attack') {
				commandsImmediatelyExecuted = ['bang', 'missed'];
				if (inArray(command, commandsImmediatelyExecuted)) {
					executeCommand();
				}
			}
		}
	} else {
		if (phase != 'throw') {
			// automaticky spustene prikazy
			var commandsImmediatelyExecuted;
			if (place == 'table') {
				// aktivacia kariet ktore su uz na stole
				commandsImmediatelyExecuted = ['howitzer', 'canteen', 'ponyexpress'];
				if (inArray(command, commandsImmediatelyExecuted)) {
					executeCommand();
				} else {
					// karty ku ktorym treba tahat kartu z balicka
					commandsImmediatelyExecuted = ['dynamite', 'jail', 'rattlesnake', 'barrel'];
					if (inArray(command, commandsImmediatelyExecuted)) {
						drawCards();
					} else {
						if (phase == 'under_attack') {
							commandsImmediatelyExecuted = ['tengallonhat', 'ironplate', 'sombrero', 'bible'];
							if (inArray(command, commandsImmediatelyExecuted)) {
								executeCommand();
							}
						}
					}
				}
			} else {
				if (phase == 'play') {
					// karty z ruky, ktore sa mozu rovno hrat
					commandsImmediatelyExecuted = ['diligenza', 'wellsfargo', 'indians', 'gatling', 'beer', 'saloon',
						'generalstore', 'poker', 'wildband', 'tornado'];
					if (inArray(command, commandsImmediatelyExecuted)) {
						executeCommand();
					} else {
						// karty ktore treba vylozit na stol
						commandsImmediatelyExecuted = ['volcanic', 'schofield', 'remington', 'revcarabine', 'winchester', 'shootgun',
							'mustang', 'appaloosa', 'hideout', 'silver', 'barrel', 'dynamite',
							'derringer', 'howitzer', 'knife', 'buffalorifle', 'pepperbox',
							'tengallonhat', 'ironplate', 'sombrero', 'bible',
							'canteen', 'ponyexpress', 'conestoga', 'cancan'];
						if (inArray(command, commandsImmediatelyExecuted)) {
							putCard();
						} else {
							// karty ku ktorym treba prihodit druhu kartu
							commandsImmediatelyExecuted = ['whisky', 'tequila'];
							if (inArray(command, commandsImmediatelyExecuted) && selectedAdditionalCard.value != 0) {
								executeCommand();
							}
						}
					}
				} else {
					// karty ktore sa pouzivaju ako obrana pri utoku
					if (phase == 'under_attack') {
						commandsImmediatelyExecuted = ['bang', 'missed', 'dodge'];
						if (inArray(command, commandsImmediatelyExecuted)) {
							executeCommand();
						}
					}
				}
			}
		} else {
			throwCard();
		}
	}
}

function ejectCard(card, type) {
	if (card) {
		card.addClassName('selected');
		card.addClassName(type);
	}
}

function rejectCard(card, type) {
	if (card) {
		card.removeClassName('selected');
		card.removeClassName(type);
	}
}

function selectCard(id, playerId, place) {
	var command = $('command').value;
	var selectedCard = $('selected-card');
	var i;
	if (command == 'brawl') {
		var playerToCard = [];
		var actualValue = selectedCard.value;
		if (actualValue) {
			var valueParts = actualValue.split(';');
			
			for (i = 0; i < valueParts.length; i++) {
				var oneItem = valueParts[i].split('-');
				playerToCard[oneItem[0]] = oneItem[1];
			}
		}
		if (playerToCard[playerId]) {
			var oldCard = $('card-' + playerToCard[playerId]);
			rejectCard(oldCard, 'brawl');
		}
		
		if (playerToCard[playerId] != id) {
			var newCard = $('card-' + id);
			ejectCard(newCard, 'brawl');
		} else {
			id = 0;
		}
		playerToCard[playerId] = id;
		
		var finalArray = [];
		for (var key in playerToCard) {
			if (typeof playerToCard[key] !== 'function') {
				finalArray.push(key + '-' + playerToCard[key]);
			}
		}
		
		var newValue = finalArray.join(';');
		selectedCard.value = newValue;
		
		var alivePlayers = $('alivePlayers').value;
		if ((alivePlayers - 1) == finalArray.length) {
			executeCommand();
		}
	} else {
		var newSelectedCard = $('card-' + id);
		var oldSelectedCardId = selectedCard.value;

		if (oldSelectedCardId == 0) {
			ejectCard(newSelectedCard, 'xxx');
			selectedCard.value = id;
		} else {
			if (oldSelectedCardId == id) {
				rejectCard(newSelectedCard, 'xxx');
				selectedCard.value = 0;
			} else {
				var oldSelectedCard = $('card-' + oldSelectedCardId);
				rejectCard(oldSelectedCard, 'xxx');
				ejectCard(newSelectedCard, 'xxx');
				selectedCard.value = id;
			}
		}	

		if (place) {
			$('place').value = place;
		} else {
			$('place').value = '';
		}
	
		// sluzi momentalne pri cat balou ako vyber karty hraca zo stola, uvidime ci to budeme oddelovat alebo to nechame tu - asi to bude musiet byt nejaka specialna metoda ktora spravi nieco ine ako select PLayer
		if (playerId) {
			selectPlayer(playerId, true);
		}

		// TODO kazdy hrac by mohol mat rozne zony ktore by sa tymto sposobom vyselektovali ako kliknutelne
		// napr. bang na vzdialenost 1 oznaci len hracov vo vzdialenosti 1 atd
	}
}

function drawCards(color) {
	var command = 'draw';
	if (color) {
		$('peyote-color').value = color;
	}
	$('command').value = command;
	executeCommand();
}

function playCard() {
	executeCommand();
}

function throwCard() {
	$('command').value = 'throw';
	var selectedPlayCard = $('selected-play-card').value;
	if (selectedPlayCard != 0) {
		executeCommand();
	} else {
		$('phase').value = 'throw';
	}
}

function putCard() {
	$('command').value = 'put';
	executeCommand();
}

function selectPlayer(id, fromSelectCard) {
	var command = $('command').value;
	if (command != 'brawl') {
		
		if (command == 'fanning' && $('selected-player').value) {
			// pri fanningu musime vybrat dvoch hracov
			$('additional-player').value = id;
			executeCommand();
		} else {
			$('selected-player').value = id;
			if (fromSelectCard) {

			} else {
				$('selected-card').value = 0;
				$('place').value = '';
			}
		}
	} else {
		selectCard(0, id, 'hand');
	}
	
	//alert(command);
	var commandsImmediatelyExecuted = ['bang', 'springfield', 'punch', 'duel',
		'pepperbox', 'knife', 'buffalorifle', 'derringer',
		'panic', 'catbalou', 'conestoga', 'cancan', 'ragtime',
		'jail', 'tequila',
		'rattlesnake', 'bounty', 'aiming', 'tomahawk', 'ghost'];
	if (inArray(command, commandsImmediatelyExecuted)) {
		executeCommand();
	} else {
		var characterName = $('character-name').value;
		if (characterName == 'calamity-janet' && command == 'missed') {
			executeCommand();
		} else if (characterName == 'jesse-jones') {
			drawCards();
		}
	}
}

function passTurn() {
	$('command').value = 'pass';
	executeCommand();
}

function lostLife() {
	$('command').value = 'life';
	executeCommand();
}

function useCharacter(id, name) {
//	alert(name);
	var useCharacter = $('use-character');
	var characterName = $('character-name');
	var characterCard = $('character-' + id);
	if (useCharacter.value == 1) {
		useCharacter.value = 0;
		characterName.value = '';
		rejectCard(characterCard, 'character');
	} else {
		useCharacter.value = 1;
		characterName.value = name;
		ejectCard(characterCard, 'character');

		// automaticke spustenie akcie podla charakteru
		var charactersImmediatelyExecutedDraw = ['jourdonnais', 'pedro-ramirez'];
		if (inArray(name, charactersImmediatelyExecutedDraw)) {
			drawCards();
		}
	}
}

function drawHighNoon() {
	$('command').value = 'draw_high_noon';
	executeCommand();
}

function useOneRoundCard(oneRoundCard) {
	//alert(oneRoundCard);
	$('command').value = 'use_one_round_card';
	// asi nie kazda jednokolova karta sa bude spustat takto, mozno bude treba vymenovat ktore ano
	executeCommand();
}

function chooseCard(cardId) {
	var possibleCount = $('possibleCount').value;
	var selectedCards = $('selected-card');
	var actualValue = selectedCards.value;
	var cardList = [];
	
	if (actualValue) {
		cardList = actualValue.split(';');
		cardList[cardList.length] = cardId;
	} else {
		cardList[0] = cardId;
	}

	var newValue = cardList.join(';');
	selectedCards.value = newValue;
	
	var card = $('choice-card-' + cardId);
	if (card) {
		ejectCard(card, 'choice');
	}
	
	if (possibleCount == cardList.length) {
		$('command').value = 'choose_cards';
		
		executeCommand();
	} else {
	//	alert(newValue);
	}
}