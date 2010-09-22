{foreach from=$game.players item=player}
	{if $loggedUser.id == $player.user.id}
		{assign var='me' value=$player}
	{else}
		<div style="border:1px;">
			{$player.user.username} - {$player.charakter.name}
		</div>
	{/if}
{/foreach}
{if $me}
	{$me.user.username} <img src="{$me.role.imageFolder}{$me.role.image}" alt="{$me.role.title}" />  <img src="{$me.charakter.imageFolder}{$me.charakter.image}" alt="{$me.charakter.name}" />

	{if $me.hand_cards}
		<div>
			{foreach from=$me.hand_cards item=handCard}
				{$handCard.title}
			{/foreach}
		</div>
	{/if}
{/if}