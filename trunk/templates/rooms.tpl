{if $rooms}
	<div class="list">
		{foreach from=$rooms item=room}
			<div class="item">
				<p><a href="{$room.url}" onclick="window.open(this.href, '_blank'); return false;">{$room.title}</a>{if $room.game} (BANG){/if}<p>
			</div>
		{/foreach}
	</div>
{/if}

{if $loggedUser.admin}
	<h3>Vytvoriť novú miestnosť</h3>
	<form action="{actualurl}" method="post">
		<input type="text" name="title" />
		<textarea name="description"></textarea>
		<input type="submit" name="create_room" value="vytvor" />
	</form>
{/if}