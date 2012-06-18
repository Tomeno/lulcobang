<?php

class LocalizeMessage {
	public function main() {
		$key = addslashes(Utils::post('key'));

		echo Localize::getMessage($key);
	}
}

?>