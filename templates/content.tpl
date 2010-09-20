<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>{if $title}{$title}{else}Bang!{/if}</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<script type="text/JavaScript" src="js/prototype.js"></script>
		<script type="text/JavaScript" src="js/bang.js"></script>
	</head>
	<body{if $bodyAdded} {$bodyAdded}{/if}>
		{if $loggedUser}<div><p>Prihlásený: <strong>{$loggedUser.username}</strong> <a href="logout.php">Odhlásiť</a></p></div>{/if}
		{$content}
	</body>
</html>