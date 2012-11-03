<form action="{actualurl}" method="post">
	<fieldset class="formular">
		<div class="field"><label><span class="label">{localize key='username'}:</span> <input type="text" value="{$loggedUser.username}" disabled="disabled" /></label></div>
		<div class="field"><label><span class="label">{localize key='password'}:</span> <input type="password" name="password" value="" /></label></div>
		<div class="field"><label><span class="label">{localize key='confirm_password'}:</span> <input type="password" name="confirm_password" value="" /></label></div>

		<div class="field"><label><span class="label">{localize key='name'}:</span> <input type="text" name="name" value="{$loggedUser.name}" /></label></div>
		<div class="field"><label><span class="label">{localize key='surname'}:</span> <input type="text" name="surname" value="{$loggedUser.surname}" /></label></div>

		{if $colors}
			<div class="field">
				<label><span class="label">{localize key='color'}</span>
					<select name="color" style="color: {$loggedUser.fontColor.code};">
						{foreach from=$colors item='color'}
							<option value="{$color.id}" style="color: {$color.code};"{if $color.id == $loggedUser.color} selected="selected"{/if}>{$color.title}</option>
						{/foreach}
					</select>
				</label>
			</div>
		{/if}
		<div class="field"><input type="submit" name="change_settings" value="CHS" /></div>
	</fieldset>
</form>