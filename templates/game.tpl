<div id="table">
	<script type="text/javascript">
		var gameBoxTimeInterval;
	</script>

	{if $game}{* && $game.status == $gameStartedStatus}*}
		{foreach from=$game.players item='player'}
			{if $loggedUser.id == $player.user.id}
				{assign var='me' value=$player}
			{/if}
		{/foreach}

		{foreach from=$game.players item='player' name=players}
			{if $game.playerOnTurn.id == $player.id && $game.playerOnTurn.id == $game.playerOnMove.id}
				{if $me.id == $player.id && $me.phase == 5}
					<a href="#" onclick="passTurn(); return false;" title="{localize key='pass'}">
						<span class="stop"></span>
					</a>
				{/if}
			{/if}
			<div id="player_0{$me|position_class:$player}" class="player">
				{if $player.winner}<div class="winner"></div>{/if}
				<div class="player_info">
					<div class="player_name">
						{if $player.id == $game.playerOnTurn.id && $game.playerOnTurn.id == $game.playerOnMove.id}
							<span class="green"></span>
						{elseif $player.id == $game.playerOnTurn.id && $game.playerOnTurn.id != $game.playerOnMove.id}
							<span class="orange"></span>
						{elseif $player.id == $game.playerOnmove.id && $game.playerOnTurn.id != $game.playerOnMove.id}
							<span class="green"></span>
						{/if}
						<a href="{$player.url}" onclick="selectPlayer({$player.id});return false;">
							{$player.user.username}
							
						</a>
					</div>
					<div class="photo">
						<a href="{$player.url}" onclick="selectPlayer({$player.id});return false;">
							{image src="static/images/photo.jpg" alt="foto" width='50' height='50'}
						</a>
						{if $me.user.need_help}
							{if $me.phase == 5 && $me.id != $player.id && $player.actual_lifes > 0}
								<div class="help">
									Klikni sem a vyber hraca
								</div>
							{/if}
						{/if}
						{*
						{if $game.playerOnTurn.id == $player.id && $me.id == $player.id}
							<a href="#" onclick="putCard(); return false;" title="Click here to put selected card"><span class="card"></span></a>
						{/if}
						*}
						{if $game.isHighNoon}
							{if $player.roleObject.isSheriff}
								<div class="card high_noon">
									{if $game.playerOnTurn.id == $player.id && $me.id == $player.id}
										<a href="#" onclick="drawHighNoon(); return false;" title="{$game.topHighNoonPile.localizedTitle} - {$game.topHighNoonPile.localizedDescription} Click here to draw high noon card">
									{/if}
									{image src=$game.topHighNoonPile.backImagePath alt='rola' width='44' height='76'}
									{if $game.playerOnTurn.id == $player.id && $me.id == $player.id}
										</a>
									{/if}
								</div>
							{/if}
						{/if}
					</div>
				</div>
				{if $game.status == $gameStartedStatus}
					<div class="row">
						<div class="lifes">
							{if $player.user.id == $me.user.id}
								<a href="#" onclick="lostLife(); return false;">
							{/if}
							{$player.actual_lifes}
							{if $player.user.id == $me.user.id}
								</a>
							{/if}
						</div>
						{if $me.actual_lifes > 0 || $me.isGhost}
							<div class="{if $player.user.id == $me.user.id}range{else}distance{/if}">
								{if $player.user.id == $me.user.id}
									{$player->getRange($game)}
								{else}
									{if $game->getDistance($me.user.username, $player.user.username)}
										{$game->getDistance($me.user.username, $player.user.username)}
									{else}
										X
									{/if}
								{/if}
							</div>
						{/if}
						<div class="char" id="character-{$player.character.id}">
							<a href="{$player.character.url}" onclick="{if $player.user.id == $me.user.id}useCharacter('{$player.character.id}', '{$player.character.alias}');{else}window.open(this.href, '_blank');{/if} return false;" title="{$player.character.name|escape}{if $player.selectedCharacter} ({localize key='play_as'} {$player.selectedCharacter.name|escape}){/if} ({$player.max_lifes}): {$player.character.localizedDescription|escape}" class="popup-source">
								{image src=$player.character.imagePath alt=$player.character.name width='44' height='76'}
							</a>
						</div>

						<div class="role">
							{if $player.roleObject.isSheriff or $player.user.id == $me.user.id or $player.actual_lifes == 0}
								<a href="{$player.roleObject.url}" onclick="window.open(this.href, '_blank'); return false;">
									{image src=$player.roleObject.imagePath alt=$player.roleObject.title width='44' height='76'}
								</a>
							{else}
								{image src=$player.roleObject.backImagePath alt='rola' width='44' height='76'}
							{/if}
						</div>
					</div>
					{assign var='baseWidth' value=240}
					{if $player.handCards}
						{assign var='cardsCount' value=$player.handCards|@count}
						{if $cardsCount > 6}
							{assign var='cardWidth' value=$baseWidth/$cardsCount}
						{else}
							{assign var='cardWidth' value=0}
						{/if}
						<div class="row cards">
							<span class="card_icon hand_cards"></span>
							{foreach from=$player.handCards item='handCard' name='handCards'}
								<div{if $player.user.id == $me.user.id or $game->getIsHNSacagaway()} id="card-{$handCard.id}"{/if} class="card"{if $cardWidth} style="width: {$cardWidth}px;"{/if}>
									{if $player.user.id == $me.user.id}
										<a class="popup-source" href="{$handCard.url}" onclick="selectCardToPlay('{$handCard.id}', '{$handCard.itemAlias}'); return false;" title="">
											{image src=$handCard.imagePath alt=$handCard.title width='44' height='76'}
											<span class="popup">
												<span class="image">{image src=$handCard.imagePath alt=$handCard.title width='88' height='152'}</span>
												<span class="text">
													<span class="title">{$handCard.title|escape}</span>
													<span class="description">{$handCard.description|escape}</span>
													<span class="usage">{$handCard.description|escape}</span>
												</span>
											</span>
										</a>
									{elseif $game->getIsHNSacagaway()}
										<a href="{$handCard.url}" onclick="selectCard('{$handCard.id}', '{$player.id}', 'hand'); return false;" title="{$handCard.title|escape}: {$handCard.description|escape}" class="popup-source">
											{image src=$handCard.imagePath alt="card" width='44' height='76'}
										</a>
									{else}
										<a href="{$player.url}" onclick="selectPlayer({$player.id});return false;">
											{image src=$handCard.backImagePath alt="card" width='44' height='76'}
										</a>
									{/if}
								</div>
							{/foreach}
						</div>
					{/if}
					{if $player.tableCards || $player.waitCards}
						{assign var='cardsCount' value=$player.tableAndWaitCardsCount}
						{if $cardsCount > 6}
							{assign var='cardWidth' value=$baseWidth/$cardsCount}
						{else}
							{assign var='cardWidth' value=0}
						{/if}
						<div class="row cards">
							<span class="card_icon table_cards"></span>
							{foreach from=$player.tableCards item='tableCard' name='tableCards'}
								<div id="card-{$tableCard.id}" class="card"{if $cardWidth} style="width: {$cardWidth}px;"{/if}>
									<a href="{$tableCard.url}" onclick="{if $player.id == $me.id}selectCardToPlay('{$tableCard.id}', '{$tableCard.itemAlias}', 'table');{else}selectCard('{$tableCard.id}', '{$player.id}', 'table');{/if} return false;" title="{$tableCard.title|escape}: {$tableCard.description|escape}" class="popup-source">
										{image src=$tableCard.imagePath alt="card" width='44' height='76'}
									</a>
								</div>
							{/foreach}
							{foreach from=$player.waitCards item='waitCard' name='waitCards'}
								<div id="card-{$waitCard.id}" class="card" {if $cardWidth} style="width: {$cardWidth}px;"{/if}>
									<a href="{$waitCard.url}" onclick="{if $player.id == $me.id}selectCardToPlay('{$waitCard.id}', '{$waitCard.itemAlias}', 'wait');{else}selectCard('{$waitCard.id}', '{$player.id}', 'wait');{/if} return false;" title="{$waitCard.title|escape}: {$waitCard.description|escape}" class="popup-source">
										{image src=$waitCard.imagePath alt="card" width='44' height='76'}
									</a>
									<div class="gray"></div>
								</div>
							{/foreach}
						</div>
					{/if}
				{else}
					{if $player.roleObject}
						<div class="role">
							{if $player.roleObject.isSheriff or $player.user.id == $me.user.id or $game.status == 3}
								<a href="{$player.roleObject.url}" onclick="window.open(this.href, '_blank'); return false;">
									{image src=$player.roleObject.imagePath alt=$player.roleObject.title width='44' height='76'}
								</a>
							{else}
								{image src=$player.roleObject.backImagePath alt='rola' width='44' height='76'}
							{/if}
						</div>
					{/if}
				{/if}
			</div>
		{/foreach}

		{if $game.status == $gameStartedStatus}
			{if $game.draw_pile}
				<div id="kopa" class="card">
					{if $game->getIsHNPeyote() && $me.textPhase == 'draw'}
						<div class="drawPile" style="width:150px;">
							{image src=$game.topDrawPile.backImagePath alt="draw pile" width='44' height='76'}
							<span class="guess red"><a href="#" onclick="drawCards('red'); return false;"><span class="hearts">&hearts;</span> <span class="diams">&diams;</span> red</a></span>
							<span class="guess black"><a href="#" onclick="drawCards('black'); return false;"><span class="spades">&spades;</span> <span class="clubs">&clubs;</span> black</a></span>
						</div>
						
					{else}
						<a href="#" onclick="drawCards();return false">{image src=$game.topDrawPile.backImagePath alt="draw pile" width='44' height='76'}</a>
						{if $me.user.need_help}
							{if $me.textPhase == 'draw'}
								<div class="help">
									{localize key='click_here_to_draw_cards'}
								</div>
							{/if}
						{/if}
					{/if}
				</div>

			{/if}
			<a id="odpad" class="card" href="#" onclick="throwCard(); return false;" title="Click here to play selected card">
				{if $game.topThrowPile}
					{image src=$game.topThrowPile.imagePath alt=$game.topThrowPile.title width='44' height='76'}
				{else}
					<span class="card play"></span>
				{/if}	
			</a>
			{*
			<a href="#" onclick="throwCard(); return false;" title="Click here to throw selected card"><span class="card throw"><img src="static/images/odpad.jpg" alt="" /></span></a>
			*}
			{if $game.isHighNoon}
				{if $game.highNoonActualCard}
					<a id="high_noon_actual_card" class="card"  href="#" onclick="useOneRoundCard('{$game.highNoonActualCard.localize_key}'); return false;" title="{$game.highNoonActualCard.localizedTitle|escape}: {$game.highNoonActualCard.localizedDescription|escape}">
						{image src=$game.highNoonActualCard.imagePath alt=$game.highNoonActualCard.title width='44' height='76'}
					</a>
				{/if}
			{/if}
			
			<div id="overlay-response"{if not $response} style="display: none;"{/if}>
				{$response}
			</div>
			<form action="{actualurl}" method="post">
				<fieldset>
					<input type="hidden" name="game" id="game" value="{$game.id}" />
					<input type="hidden" name="room" id="room" value="{$game.room}" />
					<input type="hidden" name="card" id="selected-play-card" value="" />
					<input type="hidden" name="card" id="selected-additional-card" value="" />
					<input type="hidden" name="card" id="selected-card" value="" />
					<input type="hidden" name="player" id="selected-player" value="" />
					<input type="hidden" name="command" id="command" value="" />
					<input type="hidden" name="place" id="place" value="" />
					<input type="hidden" name="use-character" id="use-character" value="" />
					<input type="hidden" name="character" id="character-name" value="" />
					<input type="hidden" name="peyote" id="peyote-color" value="" />
					<input type="hidden" name="additionalPlayer" id="additional-player" value="" />
					<input type="hidden" name="phase" id="phase" value="{$me.textPhase}" />
					<input type="hidden" name="alivePlayers" id="alivePlayers" value="{$game.alivePlayersCount}" />
				</fieldset>
			</form>
		{else}
			<div id="overlay-response"{if not $response} style="display: none;"{/if}>
				{$response}
			</div>
			
			<form action="" method="post">
				<fieldset class="formular game">
					{if $createGameAvailable}
						<div class="field"><input type="submit" value="{localize key='create_game'}" name="create" /></div>
					{elseif $game.status == 0}
						{if $joinGameAvailable}
							<div class="field"><input type="submit" value="{localize key='join_game'}" name="join" /></div>
						{elseif $startGameAvailable}
							<div class="field"><input type="submit" value="Add AI player" name="add_ai_player" /></div>
							<div class="field"><input type="submit" value="{localize key='start_game'}" name="start" /></div>
						{else}
							<div class="field"><p class="wait_message">{localize key='wait_for_game'}</p></div>
						{/if}
					{/if}
				</fieldset>
			</form>
		{/if}
	{else}
		{if $createGameAvailable}
			<form action="" method="post">
				<fieldset class="formular game">
					<div class="field"><input type="submit" value="{localize key='create_game'}" name="create" /></div>
				</fieldset>
			</form>
		{/if}
	{/if}

	<script type="text/javascript">
		clearInterval(gameBoxTimeInterval);
		{if $refreshGameBox}
			gameBoxTimeInterval = setInterval('refreshGameBox("{$game.id}", "{$room.id}")', 5000);
		{/if}
	</script>
</div>
	
	<script typ="text/javascript">
		document.fire('dom:loaded');
	</script>