{if $error}
	<p style="color:red;">{$error}<p>
{/if}
<form action="{actualurl}" method="post">
	<fieldset class="login">
		<div><label>Username: <input type="text" name="username" /></label></div>
		<div><label>Password: <input type="password" name="password" /></label></div>
		<div><input type="submit" name="login" value="login" /></div>
	</fieldset>
</form>