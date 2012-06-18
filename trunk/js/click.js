document.onclick = getClickXY;

var IE = document.all?true:false

function getClickXY(e){
	var tempX = 0;
	var tempY = 0;
	var loc = window.location.pathname;
	if (IE) {
		var additionalLeft = 0;
		var additionalTop = 0;
		if (document.documentElement && document.documentElement.scrollTop) {
			additionalTop = document.documentElement.scrollTop;
		} else if (document.body && document.body.scrollTop) {
			additionalTop = document.body.scrollTop;
		}

		if (document.documentElement && document.documentElement.scrollLeft) {
			additionalLeft = document.documentElement.scrollLeft;
		} else if (document.body && document.body.scrollLeft) {
			additionalLeft = document.body.scrollLeft;
		}
		tempX = event.clientX + additionalLeft;
		tempY = event.clientY + additionalTop;
	} else {
		tempX = e.pageX;
		tempY = e.pageY;
	}

	new Ajax.Request('services/ClickLogger.php', {
		method: 'post',
		parameters: {x: tempX, y: tempY, url: loc},

		onSuccess: function(transport) {
			newtext = transport.responseText;
			chatarea.innerHTML = chatarea.innerHTML + newtext;
			scrollArea(chatarea);
			focusToInput();
		},

		onFailure: function() {
		}
	});
}