function timedRefresh(timeoutPeriod, room) {
	//setTimeout("location.reload(true);",timeoutPeriod);
	setInterval("refreshChat(" + room +")", timeoutPeriod);
	
	chatarea = document.getElementById('chatarea');
	scrollArea(chatarea);
	
	focusToInput();
}
		
function insertEmoticon(emoticon) {
	input = document.getElementById('message');
	input.value += emoticon;
	
	focusToInput();
}

function refreshChat(room) {
	chatarea = document.getElementById('chatarea');
	
	new Ajax.Request('services/RefreshChat.php', {
		method: 'post',
		parameters: {room: room},

		onSuccess: function(transport)
		{
			newtext = transport.responseText;
			chatarea.innerHTML = newtext;
			scrollArea(chatarea);
			focusToInput();
		},
		
		onFailure: function()
		{
			chatarea.innerHTML = '<p>Na serveri nastala chyba, skúste neskôr, prosím.</p>';
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