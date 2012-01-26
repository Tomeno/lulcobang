<div id="table_wrapper">
	{if $loggedUser}<div><p>Prihlásený: <strong>{$loggedUser.username}</strong> <a href="logout.php">Odhlásiť</a></p></div>{/if}

	{$gameBox}
	
	{$chatBox}

	{*
	<div id="users" style="border:1px dashed;height:200px;overflow-y:scroll;scroll:true;padding:10px;width:20%;">
		{include file='users-box.tpl' users=$users}
	</div>
	*}
</div>

<div id="overlay-response"{if not $response} style="display: none;"{/if}>
	{$response}
</div>