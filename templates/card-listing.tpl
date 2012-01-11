<div class="listing">
	{foreach from=$cards item='card'}
		<div class="item" style="float:left; padding:5px;">
			<div class="image">
				<a href="{$card.url}">
					<img src="{$card.imagePath}" alt="{$card.title}" width="150px" />
				</a>
			</div>
			<div class="text" style="text-align:center;">
				<p><a href="{$card.url}">{$card.title}</a></p>
			</div>
		</div>
	{/foreach}
</div>