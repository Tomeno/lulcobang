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
		} else {
			$('place').value = '';
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
		command += ' ' + color;
	}
	$('command').value = command;
	executeCommand();
}

function playCard() {
	executeCommand();
}

function throwCard() {
	$('command').value = 'throw';
	executeCommand();
}

function putCard() {
	$('command').value = 'put';
	executeCommand();
}

function selectPlayer(id, fromSelectCard) {
	var command = $('command').value;
	if (command != 'brawl') {
		$('selected-player').value = id;
		if (fromSelectCard) {

		} else {
			$('selected-card').value = 0;
		}
		$('place').value = '';
	}
	
	//alert(command);
	var commandsImmediatelyExecuted = ['bang', 'springfield', 'punch', 'duel',
		'pepperbox', 'knife', 'buffalorifle', 'derringer',
		'panic', 'catbalou', 'conestoga', 'cancan', 'ragtime',
		'jail', 'tequila'];
	if (inArray(command, commandsImmediatelyExecuted)) {
		executeCommand();
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

function useCharacter(id) {
	var useCharacter = $('use-character');
	var characterCard = $('character-' + id);
	if (useCharacter.value == 1) {
		useCharacter.value = 0;
		rejectCard(characterCard, 'character');
	} else {
		useCharacter.value = 1;
		ejectCard(characterCard, 'character');
	}
}

function drawHighNoon() {
	$('command').value = 'draw_high_noon';
	executeCommand();
}
