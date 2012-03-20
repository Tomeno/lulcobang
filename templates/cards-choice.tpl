{if $possiblePickCount < $possibleCardsCount}
	<p>{localize key='pick_x_of_cards' attrs=$possiblePickCount}</p>
{else}
	<p>{localize key='you_draw_these_cards'}</p>
{/if}

<form action="{actualurl}" method="post">
	<fieldset>
		{foreach from=$possibleCards item='possibleCard'}
			{image src=$possibleCard.imagePath alt=$possibleCard.name width='150' height='230'}
			<input type="{if $possiblePickCount < $possibleCardsCount}checkbox{else}hidden{/if}" name="card[]" value="{$possibleCard.id}" />
		{/foreach}
		<input type="submit" name="choose_cards" value="{if $possiblePickCount < $possibleCardsCount}{localize key='choose'}{else}{localize key='ok'}{/if}" />
	</fieldset>
</form>