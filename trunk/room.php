<?php

require_once('include.php');

if (User::whoIsLogged() === null) {
	Utils::redirect('login.php');
}

$actualUrl =  Utils::getActualUrl();

$room = intval($_GET['id']);
if (!$room) {
	Utils::redirect('rooms.php');
}

if ($_POST && trim($_POST['message'])) {
	Chat::addMessage(trim($_POST['message']), $room);
	Utils::redirect($actualUrl);
}

$text = Chat::getMessages($room);

$emoticons = Emoticons::getEmoticons();

?>
<html>
	<head>
		<title>Bang!</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<script type="text/JavaScript" src="js/prototype.js"></script>
		<script type="text/JavaScript" src="js/bang.js"></script>
	</head>
	<body onload="JavaScript:timedRefresh(10000, <?php echo $room; ?>);">
		
		<form action="<?php echo $actualUrl; ?>" method="post">
			<div id="chatarea" style="border:1px dashed;height:200px;overflow-y:scroll;scroll:true;padding:10px;"><?php echo str_replace("\n", "<br />", $text); ?></div>
			
			<?php
			foreach ($emoticons as $emoticon) {
				echo '<a onclick="insertEmoticon(\'' . addslashes($emoticon['default']) . '\'); return false;" title="' . $emoticon['title'] . '"><img src="' . EMOTICONS_DIR . $emoticon['image'] . '" /></a>';
			}
			?>
			
			<div><input type="text" name="message" id="message" style="width:500px;" /> <input type="submit" value="send" /></div>
		</form>
	</body>
</html>