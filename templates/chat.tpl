<div id="chat">
	<div id="chatbox">
		{include file='message-box.tpl' messages=$messages}
	</div>
	<form method="post" action="{actualurl}">
		<fieldset>
			{*
			{foreach from=$emoticons item=emoticon}
				<a onclick="insertEmoticon('{$emoticon.default}'); return false;" title="{$emoticon.title}"><img src="{$emoticonDir}{$emoticon.image}" alt="" /></a>
			{/foreach}
			*}
			<input name="message" type="text" id="message" size="63" />
			<input name="submit" type="submit" id="submitmsg" value="Send" />
		</fieldset>
	</form>
</div>

<script type="text/javascript">
	chatBoxTimeInterval = setInterval('refreshChat({$room.id})', 10000);
</script>