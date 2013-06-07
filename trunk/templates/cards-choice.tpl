{if $possiblePickCount < $possibleCardsCount}
	<p>{localize key='pick_x_of_cards' attrs=$possiblePickCount}</p>
{else}
	<p>{localize key='you_draw_these_cards'}</p>
{/if}

{foreach from=$possibleCards item='possibleCard'}
	<div id="choice-card-{$possibleCard.id}" class="card choice">
		<a href="#" onclick="chooseCard('{$possibleCard.id}'); return false;">
			{image src=$possibleCard.imagePath alt=$possibleCard.name width='150' height='230'}
		</a>
	</div>
{/foreach}

<input type="hidden" name="possibleCount" id="possibleCount" value="{$possiblePickCount}" />