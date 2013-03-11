<div class="list left_list">
	{if $notValidCards}
		<div class="not_valid">
			<h2>Zatiaľ nepoužiteľné karty</h2>
			{foreach from=$notValidCards item='card'}
				<div class="item">
					<h3><a href="{$card.url}">{$card.title}</a></h3>
					<p>{$card.description}</p>
				</div>
			{/foreach}
		</div>
	{/if}
	{if $validCards}
		<div class="valid">
			<h2>Použiteľné karty</h2>
			{foreach from=$validCards item='card'}
				<div class="item">
					<h3><a href="{$card.url}">{$card.title}</a></h3>
					<p>{$card.description}</p>
				</div>
			{/foreach}
		</div>
	{/if}
</div>
	
<div class="list right_list">
	{if $notValidCharacters}
		<div class="not_valid">
			<h2>Zatiaľ nepoužiteľné charaktery</h2>
			{foreach from=$notValidCharacters item='character'}
				<div class="item">
					<h3><a href="{$character.url}">{$character.name}</a></h3>
					<p>{$character.localizedDescription}</p>
				</div>
			{/foreach}
		</div>
	{/if}
	{if $validCharacters}
		<div class="valid">
			<h2>Použiteľné charaktery</h2>
			{foreach from=$validCharacters item='character'}
				<div class="item">
					<h3><a href="{$character.url}">{$character.name}</a></h3>
					<p>{$character.localizedDescription}</p>
					<p><strong>Použitie:</strong> {$character.localizedUsage}</p>
				</div>
			{/foreach}
		</div>
	{/if}
</div>