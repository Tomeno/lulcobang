<?php

class IndexAction extends AbstractAction {

	public function  getContent() {
		$box = new StaticPageBox();
		return $box->render();
	}
}

?>