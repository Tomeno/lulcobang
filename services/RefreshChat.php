<?php

require_once('../include.php');

class RefreshChat {
	
	public function main() {
		echo Chat::getMessages(intval($_POST['room']));
	}
}

$service = new RefreshChat();
$service->main();

?>