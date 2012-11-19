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
		}
		
		if (place) {
			$('place').value = place;
		} else {
			$('place').value = '';
		}
	}	
}

function ejectCard(card, type) {
	card.addClassName('selected');
	card.addClassName(type);
}

function rejectCard(card, type) {
	card.removeClassName('selected');
	card.removeClassName(type);
}

function selectCard(id, playerId, place) {
	$('selected-card').value = id;

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

function drawCards() {
	$('command').value = 'draw';
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
	$('selected-player').value = id;
	if (fromSelectCard) {

	} else {
		$('selected-card').value = 0;
	}
	$('place').value = '';
}

function passTurn() {
	$('command').value = 'pass';
	executeCommand();
}

function lostLife() {
	$('command').value = 'life';
	executeCommand();
}

function useCharacter() {
	var useCharacter = $('use-character');
	if (useCharacter.value == 1) {
		useCharacter.value = 0;
	} else {
		useCharacter.value = 1;
	}
}