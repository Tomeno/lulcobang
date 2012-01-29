<form action="{actualurl}" method="post">
	<div><label>{localize key='username'}: <input type="text" name="username" value="{$loggedUser.username}" /></label></div>
	<div><label>{localize key='password'}: <input type="password" name="password" value="" /></label></div>
	<div><label>{localize key='confirm_password'}: <input type="password" name="confirm_password" value="" /></label></div>

	<div><label>{localize key='name'}: <input type="text" name="name" value="{$loggedUser.name}" /></label></div>
	<div><label>{localize key='surname'}: <input type="text" name="surname" value="{$loggedUser.surname}" /></label></div>

	{if $colors}
		<div>
			<label>{localize key='color'}
				<select name="color" style="color: {$loggedUser.fontColor.code};">
					{foreach from=$colors item='color'}
						<option value="{$color.id}" style="color: {$color.code};"{if $color.id == $loggedUser.color} selected="selected"{/if}>{$color.title}</option>
					{/foreach}
				</select>
			</label>
		</div>
	{/if}
	<div><input type="submit" name="change_settings" value="CHS" /></div>
</form>