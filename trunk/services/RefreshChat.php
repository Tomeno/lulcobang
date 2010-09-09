<?php

require_once('../include.php');

class RefreshChat {
	
	public function main() {
		echo Chat::getMessages();
	}
}

$service = new RefreshChat();
$service->main();

?>