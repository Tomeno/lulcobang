{if $rooms}
	<div class="list">
		{foreach from=$rooms item=room}
			<div class="item">
				<p><a href="{$room.url}">{$room.title}</a> ({if $room.game}{$room.status}{else}{localize key='room_status_free'}{/if})</p>
			</div>
		{/foreach}
	</div>
{/if}

{if $loggedUser.admin}
	<div>
		<h3>Vytvoriť novú miestnosť</h3>
		<form action="{actualurl}" method="post">
			<fieldset class="formular">
				<div class="field">
					<label>
						<span class="label">{localize key='room_name'}:</span>
						<input type="text" value="" name="title" />
					</label>
				</div>
				<div class="field">
					<label>
						<span class="label">{localize key='room_description'}:</span> 
						<textarea name="description"></textarea>
					</label>
				</div>
				<div class="field"><input type="submit" name="create_room" value="vytvor" /></div>
			</fieldset>
		</form>
	</div>
{/if}