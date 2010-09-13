<?php

require_once('../include.php');

class RefreshChat {
	
	public function main() {
		$GLOBALS['smarty']->assign('messages', Chat::getMessages(intval($_POST['room'])));
		echo $GLOBALS['smarty']->fetch('message-box.tpl');
	}
}

$service = new RefreshChat();
$service->main();

?>