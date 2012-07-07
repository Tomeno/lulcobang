<div class="logo">
	<h1><a href="{baseurl}"><span class="hdn">Bang</span></a></h1>
</div>
<div class="user_info">
	{if $loggedUser}
		<p>{localize key='logged'}: <a href="{$loggedUser.settingsUrl}" title="{localize key='change_settings'}">{if $loggedUser.name}{$loggedUser.name|escape}{/if} {if $loggedUser.surname}{$loggedUser.surname|escape}{/if} ({$loggedUser.username|escape})</a></p>
		{if $logoutPage}<a href="{$logoutPage.url}">{$logoutPage.title}</a> {/if}</span>
	{else}
		<p><a href="{$loginPage.url}">{localize key='log_in'}</a></p>
	{/if}
</div>