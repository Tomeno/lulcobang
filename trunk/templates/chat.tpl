<div id="chat">
	<div id="chatbox">
		{include file='message-box.tpl' messages=$messages}
	</div>
	<form method="post" action="{actualurl}">
		<fieldset>
			<input name="message" type="text" id="message" class="message" />
			<input name="submit" type="submit" id="submitmsg" class="submit" value="{localize key='send'}" />
		</fieldset>
	</form>
</div>

<script type="text/javascript">
	chatBoxTimeInterval = setInterval('refreshChat("{$room.id}", "{$game.id}")', 10000);
</script>