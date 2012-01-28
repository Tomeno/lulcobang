{if $game && $game.status == 1}
	<div id="table">
		{foreach from=$game.players item=player}
			{if $loggedUser.id == $player.user.id}
				{assign var='me' value=$player}
			{/if}
		{/foreach}

		{foreach from=$game.players item=player name=players}
			<div id="player_0{$me|position_class:$player}" class="player">
				<div class="player_info">
					<div class="player_name">{if $game.playerOnTurn.id == $player.id} * {/if}{$player.user.username}</div>
					<div class="photo">
						<img src="static/images/photo.jpg" alt="foto" />
					</div>
				</div>
				{if $game.status == 1}
					<div class="row">
						<div class="lifes">{$player.actual_lifes}</div>
						<p>moze to byt sposobene tym ze charakter je uz additional field</p>
						<div class="char"><img src="{$player.charakter.imageFolder}{$player.charakter.image}" alt="{$player.charakter.name}" width="22" height="38" /></div>
						<div class="role"><img src="{$player.role.imageFolder}{if $player.role.isSheriff or $player.user.id == $me.user.id or $player.actual_lifes == 0}{$player.role.image}{else}{$player.role.back}{/if}" alt="rola" width="22" height="38" /></div>
					</div>
					{if $player.hand_cards}
						{foreach from=$player.hand_cards item=handCard name=handCards}
							{if $smarty.foreach.handCards.index mod 6 == 0}<div class="row">{/if}
								<div class="card"><img src="{$handCard.imageFolder}{if $player.user.id == $me.user.id}{$handCard.image}{else}{$handCard.back}{/if}" alt="{if $player.user.id == $me.user.id}{$handCard.title}{else}card{/if}" width="22" height="38" /></div>
							{if $smarty.foreach.handCards.index mod 6 == 5 or $smarty.foreach.handCards.last}</div>{/if}
						{/foreach}
					{/if}
					{if $player.table_cards}
						<div class="row">
							{foreach from=$player.table_cards item=tableCard}
								<div class="card"><img src="{$tableCard.imageFolder}{$tableCard.image}" alt="card" width="22" height="38" /></div>
							{/foreach}
						</div>
					{/if}
				{/if}
			</div>
		{/foreach}

		{if $hokynarstvi}
			<div id="hokynarstvi">
				<div class="card"><img src="static/images/card.jpg" /></div>
				<div class="card"><img src="static/images/card.jpg" /></div>
				<div class="card"><img src="static/images/card.jpg" /></div>
				<div class="card"><img src="static/images/card.jpg" /></div>
				<div class="card"><img src="static/images/card.jpg" /></div>
				<div class="card"><img src="static/images/card.jpg" /></div>
				<div class="card"><img src="static/images/card.jpg" /></div>
				<div class="card"><img src="static/images/card.jpg" /></div>
			</div>
		{/if}

		{if $game.draw_pile}
			<div id="kopa" class="card"><img src="{$game.topDrawPile.imageFolder}{$game.topDrawPile.back}" alt="draw pile" width="22" height="38" /></div>
		{/if}
		{if $game.throw_pile}
			<div id="odpad" class="card"><img src="{$game.topThrowPile.imageFolder}{$game.topThrowPile.image}" alt="{$game.topThrowPile.title}" width="22" height="38" /></div>
		{/if}
	</div>
{else}
	<form action="{actualurl}" method="post">
		<fieldset>
			{if not $game}
				<input type="submit" value="{localize key='create_game'}" name="create" />
			{elseif $game.status == 0}
				{if $joinGameAvailable}
					<input type="submit" value="{localize key='join_game'}" name="join" />
				{/if}

				{if $startGameAvailable}
					<input type="submit" value="{localize key='start_game'}" name="start" />
				{/if}
			{/if}
		</fieldset>
	</form>
{/if}