<?php

class PageNotFoundAction extends AbstractAction {

	public function getContent() {
		$box = new PageNotFoundBox();
		return $box->render();
	}
}

?>