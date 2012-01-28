<div class="detail">
	<div class="item">
		<div class="image">
			{image src=$character.imagePath alt=$character.title width='300' height='460'}
		</div>
		<div class="title">
			<h2>{$character.name}</h2>
		</div>
		<div class="description">
			{$character.localizedDescription}
		</div>
		{if $character.relatedCards}
			<div class="related">
				<h3>{localize key="related_cards"}</h3>
				{foreach from=$character.relatedCards item='relatedCard'}
					<div class="item">
						<div class="image">
							<a href="{$relatedCard.url}">
								{image src=$relatedCard.imagePath alt=$relatedCard.title width='150' height='230'}
							</a>
						</div>
						<div class="text">
							<p><a href="{$relatedCard.url}">{$relatedCard.title}</a></p>
						</div>
					</div>
				{/foreach}
			</div>
		{/if}
	</div>
</div>