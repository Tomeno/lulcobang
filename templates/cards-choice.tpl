{* TODO ak ma tolko kariet kolko moze vybrat, bude ina hlaska a aj checkboxy zmiznu, len to potvrdi *}
<p>Potiahol si tieto karty, mozes si vybrat 2</p>

<form action="{actualurl}" method="post">
	<fieldset>
		{foreach from=$possibleCards item='possibleCard'}
			{image src=$possibleCard.imagePath alt=$possibleCard.name width='150' height='230'}
			{* TODO toto tu nebude, namiesto toho bude hidden input *}
			<input type="checkbox" name="card[]" value="{$possibleCard.id}" />
		{/foreach}
		<input type="submit" name="choose_cards" value="{localize key='choose'}" />
	</fieldset>
</form>