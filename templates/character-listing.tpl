<div class="listing">
	{foreach from=$characters item='character'}
		<div class="item">
			<div class="image">
				<a href="{$character.url}">
					{image src=$character.imagePath alt=$character.name width='150' height='230'}
				</a>
			</div>
			<div class="text">
				<p><a href="{$character.url}">{$character.name}</a></p>
			</div>
		</div>
	{/foreach}
</div>