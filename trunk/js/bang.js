function timedRefresh(timeoutPeriod, room) {
	//setTimeout("location.reload(true);",timeoutPeriod);
	setInterval("refreshChat(" + room + ")", timeoutPeriod);
	//setInterval("refreshUsersBox(" + room + ")", timeoutPeriod);
	setInterval("refreshGameBox(" + room + ")", timeoutPeriod);
	
	chatarea = document.getElementById('chatbox');
	scrollArea(chatarea);
	
	focusToInput();
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

function refreshGameBox(room) {
	gamebox = document.getElementById('table');
	
	new Ajax.Request('services/RefreshGame.php', {
		method: 'post',
		parameters: {room: room},
		
		onSuccess: function(transport) {
			newtext = transport.responseText;
			gamebox.innerHTML = newtext;
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