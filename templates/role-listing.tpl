<div class="listing">
	{foreach from=$roles item='role'}
		<div class="item">
			<div class="image">
				<a href="{$role.url}">
					{image src=$role.imagePath alt=$role.title width='150' height='230'}
				</a>
			</div>
			<div class="text">
				<p><a href="{$role.url}">{$role.title}</a></p>
			</div>
		</div>
	{/foreach}
</div>