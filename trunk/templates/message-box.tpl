<div id="chatarea" style="border:1px dashed;height:200px;overflow-y:scroll;scroll:true;padding:10px;">
	{foreach from=$messages item=message}
		<p>{if $message.username}<span style="color: {$message.color}">{$message.username}: </span>{/if}{$message.text|replace_emoticons|linkify}</p>
	{/foreach}
</div>