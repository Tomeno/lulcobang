{if $loggedUser}
	<span>{localize key='logged'}: <a href="{$loggedUser.settingsUrl}">{if $loggedUser.name}{$loggedUser.name}{/if} {if $loggedUser.surname}{$loggedUser.surname}{/if} ({$loggedUser.username})</a>
	{if $logoutPage} | <a href="{$logoutPage.url}">{$logoutPage.title}</a> | {/if}</span>
{else}
{/if}

{if $languages}
	{foreach from=$languages item='language' name='languages'}
		<a href="{$language.correspondingUrl}">
			{if $language.shortcut == $actualLanguage}<strong>{/if}
			{$language.title}
			{if $language.shortcut == $actualLanguage}</strong>{/if}
			{if not $smarty.foreach.languages.last} | {/if}
		</a>
	{/foreach}
{/if}