<html>
	<head>
		<title>{if $title}{$title}{else}Bang!{/if}</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<script type="text/JavaScript" src="js/prototype.js"></script>
		<script type="text/JavaScript" src="js/bang.js"></script>
	</head>
	<body{if $bodyAdded} {$bodyAdded}{/if}>
		{$content}
	</body>
</html>