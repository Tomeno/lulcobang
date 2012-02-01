<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>{if $title}{$title}{else}Bang!{/if}</title>
		<base href="{$baseUrl}" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<script type="text/JavaScript" src="js/prototype.js"></script>
		<script type="text/JavaScript" src="js/prototip.js"></script>
		<script type="text/JavaScript" src="js/bang.js"></script>
		<link type="text/css" rel="stylesheet" media="all" href="static/css/style.css" />
		<link type="text/css" rel="stylesheet" media="all" href="static/css/prototip.css" />
	</head>
	<body{if $bodyAdded} {$bodyAdded}{/if}>
		<div id="content">
			{$upperPart}
			{$content}
			<div class="clear"></div>
			<p>Copyright 2010 &copy; Michal Lulčo | Design by <a href="http://clira.sk/" onclick="window.open('http://clira.sk/', '_blank'); return false;">Clira</a></p>
		</div>
	</body>
</html>