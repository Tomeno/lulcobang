{if $error}
	<p style="color:red;">{$error}<p>
{/if}
<form action="{actualurl}" method="post">
	<fieldset class="login">
		<div><label>{localize key='username'}: <input type="text" name="username" /></label></div>
		<div><label>{localize key='password'}: <input type="password" name="password" /></label></div>
		<p>{localize key='login_notice'}</p>
		<div><input type="submit" name="login" value="{localize key='login'}" /></div>
	</fieldset>
</form>