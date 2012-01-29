{if $pages}
	<ul>
		{foreach from=$pages item='page'}
			<li><a href="{$page.url}">{$page.title}</a></li>
		{/foreach}
	</ul>
{/if}
