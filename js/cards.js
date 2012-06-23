function selectCard(id, type, playerId, place) {
	$('selected-card').value = id;

	// ak vyberame kartu hraca mimo aktualneho, nezada sa typ - neprepise sa tym padom command ktory chce aktualny hrac spravit
	if (type) {
		type = type.replace('-', '');
		$('command').value = type;
	}

	if (place) {
		$('place').value = place;
	} else {
		$('place').value = '';
	}
	
	// sluzi momentalne pri cat balou ako vyber karty hraca zo stola, uvidime ci to budeme oddelovat alebo to nechame tu - asi to bude musiet byt nejaka specialna metoda ktora spravi nieco ine ako select PLayer
	if (playerId) {
		selectPlayer(playerId);
	}

	// TODO show help message - vypinatelne aby to furt neotravovalo - v  html to aj tak bude cele predgenerovane cize si moze kliknut na nejaky info kruzok

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

function selectPlayer(id) {
	$('selected-player').value = id;
	// zrusim nastavenie karty - neviem ci toto je spravny krok ale pre cat balou to potrebujeme

	// pre paniku to nefunguje :) ak vyberam hracovu kartu zo stola :)
//	$('selected-card').value = 0;
	executeCommand();
}

function passTurn() {
	$('command').value = 'pass';
	executeCommand();
}

function lostLife() {
	$('command').value = 'life';
	executeCommand();
}