<p>Your role is xy and you have these possible characters</p>
<form action="{actualurl}" method="post">
	<fieldset>
		{foreach from=$possibleCharacters item='possibleCharacter'}
			{image src=$possibleCharacter.imagePath alt=$possibleCharacter.name width='150' height='230'}
			<input type="radio" name="character" value="{$possibleCharacter.id}" />
		{/foreach}
		<input type="submit" name="choose_character" value="{localization key='choose'}" />
	</fieldset>
</form>