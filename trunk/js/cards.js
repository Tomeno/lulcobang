function selectCard(id, type, playerId, place) {
	$('selected-card').value = id;

	// ak vyberame kartu hraca mimo aktualneho, nezada sa typ - neprepise sa tym padom command ktory chce aktualny hrac spravit
	if (type) {
		type = type.replace('-', '');
		$('command').value = type;
	}

	// sluzi momentalne pri cat balou ako vyber karty hraca zo stola, uvidime ci to budeme oddelovat alebo to nechame tu
	if (playerId) {
		selectPlayer(playerId);
	}

	if (place) {
		$('place').value = place;
	} else {
		$('place').value = '';
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
	$('selected-card').value = 0;
	executeCommand();
}

function passTurn() {
	$('command').value = 'pass';
	executeCommand();
}