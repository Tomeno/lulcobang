<?php

require_once('include.php');

$loggedUser = User::whoIsLogged();
if ($loggedUser === null) {
	Utils::redirect('login.php');
}

if ($_POST['create_room']) {
	$room = Room::addRoom(addslashes($_POST['title']), addslashes($_POST['description']));
	Utils::redirect('room.php?id=' . $room);
}

$rooms = Room::getRooms();
$actualUrl =  Utils::getActualUrl();

?>
<html>
	<head>
		<title>Rooms | Bang!</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	</head>
	<body>
	<?php
	foreach ($rooms as $room) {
		echo '<p><a href="room.php?id=' . $room['id'] . '">' . $room['title'] . '</a><p>';
	}
	?>
	<?php if ($loggedUser['admin']): ?>
	<h3>Vytvoriť novú miestnosť</h3>
	<form action="<?php echo $actualUrl; ?>" method="post">
		<input type="text" name="title" />
		<textarea name="description"></textarea>
		<input type="submit" name="create_room" value="vytvor" />
	</form>
	<?php endif; ?>
	</body>
</html>