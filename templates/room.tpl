<form action="{actualurl}" method="post">
	<fieldset>
		{if $game}
			<div id="game" style="border:1px dashed; height:200px;">
				{include file='game.tpl' game=$game}
			</div>
		{/if}
	
		<div id="chatarea" style="border:1px dashed;height:200px;overflow-y:scroll;scroll:true;padding:10px;float:left;width:75%;">
			{include file='message-box.tpl' messages=$messages}
		</div>
		
		<div id="users" style="border:1px dashed;height:200px;overflow-y:scroll;scroll:true;padding:10px;width:20%;">
			{include file='users-box.tpl' users=$users}
		</div>
		
		{foreach from=$emoticons item=emoticon}
			<a onclick="insertEmoticon('{$emoticon.default}'); return false;" title="{$emoticon.title}"><img src="{$emoticonDir}{$emoticon.image}" alt="" /></a>
		{/foreach}
		
		<div><input type="text" name="message" id="message" style="width:500px;" /> <input type="submit" value="send" /></div>
	</fieldset>
</form>