<form action="{actualurl}" method="post">
	{if $error}
		<p style="color:red;">{$error}<p>
	{/if}

	<div><label>Username: <input type="text" name="username" /></label></div>
	<div><label>Password: <input type="password" name="password" /></label></div>
	<div><input type="submit" name="login" value="login" /></div>
</form>
