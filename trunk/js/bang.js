function timedRefresh(timeoutPeriod, room) {
	//setTimeout("location.reload(true);",timeoutPeriod);
//	setInterval("refreshChat(" + room + ")", timeoutPeriod);
	//setInterval("refreshUsersBox(" + room + ")", timeoutPeriod);
	setInterval("refreshGameBox(" + room + ")", timeoutPeriod);
	
//	chatarea = document.getElementById('chatbox');
//	scrollArea(chatarea);
	
//	focusToInput();
}
		
function insertEmoticon(emoticon) {
	input = document.getElementById('message');
	input.value += emoticon;
	
	focusToInput();
}

function refreshChat(room) {
	chatarea = document.getElementById('chatbox');
	
	new Ajax.Request('services/RefreshChat.php', {
		method: 'post',
		parameters: {room: room},

		onSuccess: function(transport) {
			newtext = transport.responseText;
			chatarea.innerHTML = chatarea.innerHTML + newtext;
			scrollArea(chatarea);
			focusToInput();
		},
		
		onFailure: function() {
			chatarea.innerHTML = '<p>Na serveri nastala chyba, skúste neskôr, prosím.</p>';
		}
	});
	return false;
}

function refreshUsersBox(room) {
	usersbox = document.getElementById('users');
	
	new Ajax.Request('services/RefreshUsersBox.php', {
		method: 'post',
		parameters: {room: room},
		
		onSuccess: function(transport) {
			newtext = transport.responseText;
			usersbox.innerHTML = newtext;
		},
		
		onFailure: function() {
			usersbox.innerHTML = '<p>Na serveri nastala chyba, skúste neskôr, prosím.</p>';
		}
	});
	return false;
}

function refreshGameBox(game) {
	gamebox = document.getElementById('table');
	
	new Ajax.Request('services/RefreshGame.php', {
		method: 'post',
		parameters: {game: game},
		
		onSuccess: function(transport) {
			newtext = transport.responseText;
			gamebox.replace(newtext);
		},
		
		onFailure: function() {
			gamebox.innerHTML = '<p>Na serveri nastala chyba, skúste neskôr, prosím.</p>';
		}
	});
	return false;
}

function scrollArea(area) {
	area.scrollTop = area.scrollHeight;
}

function focusToInput() {
	input = document.getElementById('message');
	input.focus();
}

document.observe('dom:loaded', function() {
	$$('.popup').each(function(el) {
		new Tip(el.down('.popup-source'), el.down('.popup-target'), {
			hook: {tip: 'bottomLeft', target: 'topRight'},
			offset: {x: -25, y: 25},
			hideOn: {element: 'body', event: 'click'},
			images: '/fileadmin/template/images/prototip/styles/default/',
			javascript: '',
			hideAfter: 0.500,
			hideOthers: true,
			border: 0,
			radius: 0,
			delay: 0,
			width: 350,
			closeButton: false
		});

		el.down('.popup-source').observe('prototip:shown', function() {
			document.onclick = Tips.hideAll;
		});
	});
});

function getLocalizedMessage(key) {
	new Ajax.Request('services/LocalizeMessage.php', {
		method: 'post',
		parameters: {key: key},

		onSuccess: function(transport) {
			alert(transport.responseText);
		},

		onFailure: function() {
		}
	});
	return true;
}

function executeCommand() {
	var game = $('game').value;
	var card = $('selected-card').value;
	var player = $('selected-player').value;
	var command = $('command').value;
	var place = $('place').value;

	new Ajax.Request('services/ExecuteCommand.php', {
		method: 'post',
		parameters: {game: game, card: card, player: player, command: command, place: place},

		onSuccess: function(transport) {
			// temporary reload page after execute command
			refreshGameBox(game);
			// alert(transport.responseText);
		}
	});
}