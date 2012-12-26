<?php

class IndexAction extends AbstractAction {

	public function  getContent() {
		$box = new IndexBox();
		return $box->render();
	}
}

?>