<?php

require_once('include.php');

if (User::whoIsLogged()) {
	Utils::redirect('index.php');
}

if ($_POST['login']) {
	
	User::userLogin();
}



?>
<html>
	<head>
		<title>Login | Bang!</title>
	</head>
	<body>
		<form action="login.php" method="post">
			<div><label>Username: <input type="text" name="username" /></label></div>
			<div><label>Password: <input type="password" name="password" /></label></div>
			<div><input type="submit" name="login" value="login" /></div>
		</form>
	</body>
</html>