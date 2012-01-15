<div class="detail" style="padding:10px;">
	<div class="item">
		<div class="image" style="float:left;padding-right:10px;">
			{image src=$card.imagePath alt=$card.title width='300' height='460'}
		</div>
		<div class="title">
			<h2 style="font-size:40px;">{$card.title}</h2>
		</div>
		<div class="description">
			{$card.description}
		</div>
		{if $card.relatedCards}
			<div class="related">
				{foreach from=$card.relatedCards item='relatedCard'}
					<div class="item" style="float:left; padding:5px;">
						<div class="image">
							<a href="{$relatedCard.url}">
								{image src=$relatedCard.imagePath alt=$relatedCard.title width='150' height='230'}
							</a>
						</div>
						<div class="text" style="text-align:center;">
							<p><a href="{$relatedCard.url}">{$relatedCard.title}</a></p>
						</div>
					</div>
				{/foreach}
			</div>
		{/if}
	</div>
</div>