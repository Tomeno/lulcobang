{foreach from=$messages item=message}
	<p>{if $message.username}<span style="color: {$message.color}">{$message.username}: </span>{/if}{$message.text|replace_emoticons|linkify}</p>
{/foreach}