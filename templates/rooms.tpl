{foreach from=$rooms item=room}
	<p><a href="room.php?id={$room.id}" target="_blank">{$room.title}</a>{if $room.game} (BANG){/if}<p>
{/foreach}

{if $loggedUser.admin}
	<h3>Vytvoriť novú miestnosť</h3>
	<form action="{actualurl}" method="post">
		<input type="text" name="title" />
		<textarea name="description"></textarea>
		<input type="submit" name="create_room" value="vytvor" />
	</form>
{/if}