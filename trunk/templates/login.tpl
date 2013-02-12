
<form action="{actualurl}" method="post">
	<fieldset class="formular">
		{if $errors}
			<div class="error">
				{foreach from=$errors item='error'}
					<p>{$error}</p>
				{/foreach}
			</div>
		{/if}
		<div class="field"><label><span class="label">{localize key='username'}: </span><input type="text" name="username" value="{$username}" /></label></div>
		<div class="field"><label><span class="label">{localize key='password'}: </span><input type="password" name="password" /></label></div>
		<div class="field"><label><input type="checkbox" name="remember" value="1" class="remember_checkbox" /><span class="checkbox label">{localize key='remember'}</span></label></div>
		<br />
		<p class="notice">{localize key='login_notice'}</p>
		<div class="field"><input type="submit" name="login" value="{localize key='login'}" /></div>
	</fieldset>
</form>