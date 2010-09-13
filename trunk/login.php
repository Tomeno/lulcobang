<?php

require_once('include.php');

if (User::whoIsLogged()) {
	Utils::redirect('rooms.php');
}

if ($_POST['login']) {
	
	$retVal = User::userLogin();
}

?>
<html>
	<head>
		<title>Login | Bang!</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	</head>
	<body>
		<form action="login.php" method="post">
			<?php
			if ($retVal) {
				echo '<p style="color:red;">' . $retVal . '<p>';
			}
			?>
			<div><label>Username: <input type="text" name="username" /></label></div>
			<div><label>Password: <input type="password" name="password" /></label></div>
			<div><input type="submit" name="login" value="login" /></div>
		</form>
	</body>
</html>