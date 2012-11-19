{foreach from=$messages item=message}
	<p>{$message.tstamp|human_date} {if $message.username}<span style="color: {$message.color}">{$message.username}: </span>{/if}{$message.text|linkify}</p>
{/foreach}