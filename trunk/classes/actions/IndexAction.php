<?php

class IndexAction extends AbstractAction {

	public function  getContent() {
		$page = PageActionMap::getPageByTypeAndLanguage('rooms');
		$url = PageActionMap::createUrl($page['alias']);

		Utils::redirect($url);
	}
}

?>