<div class="detail">
	<div class="item">
		<div class="image">
			{image src=$card.imagePath alt=$card.title width='300' height='460'}
		</div>
		<div class="title">
			<h2>{$card.title}</h2>
		</div>
		<div class="description">
			{$card.description}
		</div>
		{if $card.relatedCards}
			<div class="related">
				<h3>{localize key="related_cards"}</h3>
				{foreach from=$card.relatedCards item='relatedCard'}
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
		{if $card.relatedCharacters}
			<div class="related">
				<h3>{localize key="related_characters"}</h3>
				{foreach from=$card.relatedCharacters item='relatedCharacter'}
					<div class="item">
						<div class="image">
							<a href="{$relatedCharacter.url}">
								{image src=$relatedCharacter.imagePath alt=$relatedCharacter.title width='150' height='230'}
							</a>
						</div>
						<div class="text">
							<p><a href="{$relatedCharacter.url}">{$relatedCharacter.title}</a></p>
						</div>
					</div>
				{/foreach}
			</div>
		{/if}
	</div>
</div>