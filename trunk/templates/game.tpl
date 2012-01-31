{if $game && $game.status == $gameStartedStatus}
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
						{image src="static/images/photo.jpg" alt="foto"}
					</div>
				</div>
				{if $game.status == $gameStartedStatus}
					<div class="row">
						<div class="lifes">{$player.actual_lifes}</div>
						<div class="char">
							<a href="{$player.character.url}" onclick="window.open(this.href, '_blank'); return false;">{image src=$player.character.imagePath alt=$player.character.name width="22" height="38"}</a>
						</div>
						<div class="role">
							{if $player.roleObject.isSheriff or $player.user.id == $me.user.id or $player.actual_lifes == 0}
								{image src=$player.roleObject.imagePath alt=$player.roleObject.title width='22' height='38'}
							{else}
								{image src=$player.roleObject.backImagePath alt='rola' width='22' height='38'}
							{/if}
						</div>
					</div>
					{if $player.handCards}
						{foreach from=$player.handCards item=handCard name=handCards}
							{if $smarty.foreach.handCards.index mod 6 == 0}<div class="row">{/if}
								<div class="card">
									{if $player.user.id == $me.user.id}
										<a href="{$handCard.url}" onclick="window.open(this.href, '_blank'); return false;">{image src=$handCard.imagePath alt=$handCard.title width="22" height="38"}</a>
									{else}
										{image src=$handCard.backImagePath alt="card" width="22" height="38"}
									{/if}
								</div>
							{if $smarty.foreach.handCards.index mod 6 == 5 or $smarty.foreach.handCards.last}</div>{/if}
						{/foreach}
					{/if}
					{if $player.tableCards}
						<div class="row">
							{foreach from=$player.tableCards item=tableCard}
								<div class="card">{image src=$tableCard.imagePath alt="card" width="22" height="38"}</div>
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
			<div id="kopa" class="card">
				{image src=$game.topDrawPile.backImagePath alt="draw pile" width="22" height="38"}
			</div>
		{/if}
		{if $game.throw_pile}
			<div id="odpad" class="card">
				{image src=$game.topThrowPile.imagePath alt=$game.topThrowPile.title width="22" height="38"}
			</div>
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