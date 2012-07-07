{if $pages}
	<div class="menu">
		<ul>
			{foreach from=$pages item='page'}
				<li{if $page.sel} class="sel"{/if}><a href="{$page.url}">{$page.title}</a></li>
			{/foreach}
		</ul>
	</div>
{/if}