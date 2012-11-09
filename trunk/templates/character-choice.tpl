{* TODO localize *}

<p>Your role is {$player.roleObject.localizedTitle}</p>
<p>You can choice one of these character:</p>
<form action="{actualurl}" method="post">
	<fieldset>
		{foreach from=$possibleCharacters item='possibleCharacter'}
			{image src=$possibleCharacter.imagePath alt=$possibleCharacter.name width='150' height='230'}
			<input type="radio" name="character" value="{$possibleCharacter.id}" />
		{/foreach}
		<input type="submit" name="choose_character" value="{localize key='choose'}" />
	</fieldset>
</form>