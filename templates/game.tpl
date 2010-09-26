{if $game.status == 1}
	{foreach from=$game.players item=player}
		{if $loggedUser.id == $player.user.id}
			{assign var='me' value=$player}
		{else}
			<div style="border:1px;float:left;">
				{$player.user.username} {if $player.role.isSheriff} <img src="{$player.role.imageFolder}{$player.role.image}" /> {/if}{$player.charakter.name} <img src="{$player.charakter.imageFolder}{$player.charakter.image}" alt="{$player.charakter.name}" />
				{if $player.table_cards}
					{foreach from=$player.table_cards item=tableCard}
						<img src="{$tableCard.imageFolder}{$tableCard.image}" alt="{$tableCard.title}" />
					{/foreach}
				{/if}
			</div>
		{/if}
	{/foreach}
	{if $me}
		<div style="clear:both;"></div>
		{$me.user.username} <img src="{$me.role.imageFolder}{$me.role.image}" alt="{$me.role.title}" />  <img src="{$me.charakter.imageFolder}{$me.charakter.image}" alt="{$me.charakter.name}" />
	
		{if $me.hand_cards}
			<div>
				<h3>Karty na ruke</h3>
				{foreach from=$me.hand_cards item=handCard}
					<img src="{$handCard.imageFolder}{$handCard.image}" alt="{$handCard.title}" />
				{/foreach}
			</div>
		{/if}
		
		{if $me.table_cards}
			<div>
				<h3>Karty na stole</h3>
				{foreach from=$me.table_cards item=tableCard}
					<img src="{$tableCard.imageFolder}{$tableCard.image}" alt="{$tableCard.title}" />
				{/foreach}
			</div>
		{/if}
		
	{/if}
	
	{if $game.topThrowPile}
		<div style="float:right;">
			<img src="{$game.topThrowPile.imageFolder}{$game.topThrowPile.image}" />
		</div>
	{/if}
	
{else}
	{foreach from=$game.players item=player}
		{$player.user.username}
	{/foreach}
{/if}