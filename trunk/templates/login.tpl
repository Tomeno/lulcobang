{if $error}
	<p style="color:red;">{$error}<p>
{/if}
<form action="{actualurl}" method="post">
	<fieldset class="formular">
		<div class="field"><label><span class="label">{localize key='username'}: </span><input type="text" name="username" /></label></div>
		<div class="field"><label><span class="label">{localize key='password'}: </span><input type="password" name="password" /></label></div>
		<p class="notice">{localize key='login_notice'}</p>
		<div class="field"><input type="submit" name="login" value="{localize key='login'}" /></div>
	</fieldset>
</form>