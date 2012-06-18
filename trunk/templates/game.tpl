{if $game && $game.status == $gameStartedStatus}
	<div id="table">
		{foreach from=$game.players item='player'}
			{if $loggedUser.id == $player.user.id}
				{assign var='me' value=$player}
			{/if}
		{/foreach}

		{foreach from=$game.players item='player' name=players}
			<div id="player_0{$me|position_class:$player}" class="player">
				<div class="player_info">
					<div class="player_name">
						{if $game.playerOnTurn.id == $player.id && $me.id == $player.id}
							<a href="#" onclick="passTurn(); return false;">
								<span class="pass">{localize key='pass'}</span>
							</a>
						{/if}
						<a href="{$player.url}" onclick="selectPlayer({$player.id});return false;">
							{if $game.playerOnTurn.id == $player.id} * {/if}{$player.user.username}
						</a>
					</div>
					<div class="photo">
						<a href="{$player.url}" onclick="selectPlayer({$player.id});return false;">
							{image src="static/images/photo.jpg" alt="foto" width='50' height='50'}
						</a>
						{if $game.playerOnTurn.id == $player.id && $me.id == $player.id}
							<a href="#" onclick="putCard(); return false;" title="Click here to put selected card"><span class="card"></span></a>
						{/if}
					</div>
				</div>
				{if $game.status == $gameStartedStatus}
					<div class="row">
						<div class="lifes">{$player.actual_lifes}</div>
						<div class="char popup">
							<a href="{$player.character.url}" onclick="window.open(this.href, '_blank'); return false;" class="popup-source">
								{image src=$player.character.imagePath alt=$player.character.name width='44' height='76'}
							</a>
							<div class="popup-target" style="display:none;">
								<div class="popup-character">
									{image src=$player.character.imagePath alt=$player.character.name width='66' height='114'}
									<h4>{$player.character.name}</h4>
									<p>{$player.character.localizedDescription}</p>
								</div>
							</div>
						</div>

						<div class="role{if $player.roleObject.isSheriff or $player.user.id == $me.user.id or $player.actual_lifes == 0} popup{/if}">
							{if $player.roleObject.isSheriff or $player.user.id == $me.user.id or $player.actual_lifes == 0}
								<a href="{$player.roleObject.url}" onclick="window.open(this.href, '_blank'); return false;" class="popup-source">
									{image src=$player.roleObject.imagePath alt=$player.roleObject.title width='44' height='76'}
								</a>
								<div class="popup-target" style="display:none;">
									<div class="popup-role">
										{image src=$player.roleObject.imagePath alt=$player.roleObject.title width='66' height='114'}
										<h4>{$player.roleObject.localizedTitle}</h4>
										<p>{$player.roleObject.localizedDescription}</p>
									</div>
								</div>
							{else}
								{image src=$player.roleObject.backImagePath alt='rola' width='44' height='76'}
							{/if}
						</div>
					</div>
					{if $player.handCards}
						{foreach from=$player.handCards item='handCard' name='handCards'}
							{if $smarty.foreach.handCards.index mod 6 == 0}<div class="row">{/if}
								<div class="card{if $player.user.id == $me.user.id} popup{/if}">
									{if $player.user.id == $me.user.id}
										<a href="{$handCard.url}" onclick="selectCard('{$handCard.id}', '{$handCard.itemAlias}'); return false;" class="popup-source">
											{image src=$handCard.imagePath alt=$handCard.title width='44' height='76'}
										</a>
										<div class="popup-target" style="display:none;">
											<div class="popup-card">
												{image src=$handCard.imagePath alt=$handCard.title width='66' height='114'}
												<h4>{$handCard.title}</h4>
												<p>{$handCard.description}</p>
											</div>
										</div>
									{else}
										{image src=$handCard.backImagePath alt="card" width='44' height='76'}
									{/if}
								</div>
							{if $smarty.foreach.handCards.index mod 6 == 5 or $smarty.foreach.handCards.last}</div>{/if}
						{/foreach}
					{/if}
					{if $player.tableCards}
						{foreach from=$player.tableCards item='tableCard' name='tableCards'}
							{if $smarty.foreach.tableCards.index mod 6 == 0}<div class="row">{/if}
								<div class="card popup">
									<a href="{$tableCard.url}" onclick="{if $player.id == $me.id}selectCard('{$tableCard.id}', '{$tableCard.itemAlias}', 0, 'table');{else}selectCard('{$tableCard.id}', '', '{$player.id}');{/if} return false;" class="popup-source">
										{image src=$tableCard.imagePath alt="card" width='44' height='76'}
									</a>
									<div class="popup-target" style="display:none;">
										<div class="popup-card">
											{image src=$tableCard.imagePath alt=$tableCard.title width='66' height='114'}
											<h4>{$tableCard.title}</h4>
											<p>{$tableCard.description}</p>
										</div>
									</div>
								</div>
							{if $smarty.foreach.tableCards.index mod 6 == 5 or $smarty.foreach.tableCards.last}</div>{/if}
						{/foreach}
					{/if}
					{if $player.waitCards}
						{foreach from=$player.waitCards item='waitCard' name='waitCards'}
							{if $smarty.foreach.waitCards.index mod 6 == 0}<div class="row">{/if}
								<div class="card popup">
									<a href="{$waitCard.url}" onclick="{if $player.id == $me.id}selectCard('{$waitCard.id}', '{$waitCard.itemAlias}', 0, 'wait');{else}selectCard('{$waitCard.id}', '', '{$player.id}');{/if} return false;" class="popup-source">
										{image src=$waitCard.imagePath alt="card" width='44' height='76'}
									</a>
									<div class="popup-target" style="display:none;">
										<div class="popup-card">
											{image src=$waitCard.imagePath alt=$waitCard.title width='66' height='114'}
											<h4>{$waitCard.title}</h4>
											<p>{$waitCard.description}</p>
										</div>
									</div>
									<div class="gray"></div>
								</div>
							{if $smarty.foreach.waitCards.index mod 6 == 5 or $smarty.foreach.waitCards.last}</div>{/if}
						{/foreach}
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
				<a href="#" onclick="drawCards();return false">{image src=$game.topDrawPile.backImagePath alt="draw pile" width='44' height='76'}</a>
			</div>
		{/if}
		{if $game.topThrowPile}
			<div id="odpad" class="card popup">
				<a href="{$game.topThrowPile.url}" onclick="window.open(this.href, '_blank'); return false;" class="popup-source">
					{image src=$game.topThrowPile.imagePath alt=$game.topThrowPile.title width='44' height='76'}
				</a>
				<div class="popup-target" style="display:none;">
					<div class="popup-card">
						{image src=$game.topThrowPile.imagePath alt=$game.topThrowPile.title width='66' height='114'}
						<h4>{$game.topThrowPile.title}</h4>
						<p>{$game.topThrowPile.description}</p>
					</div>
				</div>
			</div>
		{/if}
		<a href="#" onclick="throwCard(); return false;" title="Click here to throw selected card"><span class="card throw"><img src="static/images/odpad.jpg" /></span></a>
		<a href="#" onclick="playCard(); return false;" title="Click here to play selected card"><span class="card play"></span></a>
	</div>
	<form action="{actualurl}" method="post">
		<fieldset>
			<input type="hidden" name="game" id="game" value="{$game.id}" />
			<input type="hidden" name="card" id="selected-card" value="" />
			<input type="hidden" name="player" id="selected-player" value="" />
			<input type="hidden" name="command" id="command" value="" />
			<input type="hidden" name="place" id="place" value="" />
		</fieldset>
	</form>
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