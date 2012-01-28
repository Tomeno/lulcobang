<div class="listing">
	{foreach from=$cards item='card'}
		<div class="item">
			<div class="image">
				<a href="{$card.url}">
					{image src=$card.imagePath alt=$card.title width='150' height='230'}
				</a>
			</div>
			<div class="text">
				<p><a href="{$card.url}">{$card.title}</a></p>
			</div>
		</div>
	{/foreach}
</div>