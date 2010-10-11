<div id="table_wrapper">
	{if $loggedUser}<div><p>Prihlásený: <strong>{$loggedUser.username}</strong> <a href="logout.php">Odhlásiť</a></p></div>{/if}
	<div id="table">
		{include file='game.tpl' game=$game}
	</div>
	
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
	{*
	<div id="users" style="border:1px dashed;height:200px;overflow-y:scroll;scroll:true;padding:10px;width:20%;">
		{include file='users-box.tpl' users=$users}
	</div>
	*}
</div>