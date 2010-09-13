<form action="{actualUrl}" method="post">
	
	{include file='message-box.tpl' messages=$messages}
	
	{foreach from=$emoticons item=emoticon}
		<a onclick="insertEmoticon('{$emoticon.default}'); return false;" title="{$emoticon.title}"><img src="{$emoticonDir}{$emoticon.image}" /></a>
	{/foreach}
	
	<div><input type="text" name="message" id="message" style="width:500px;" /> <input type="submit" value="send" /></div>
</form>