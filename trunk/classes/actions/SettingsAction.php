<?php

class SettingsAction extends AbstractAction {
	public function getContent() {
		$box = new SettingsBox();
		return $box->render();
	}
}

?>