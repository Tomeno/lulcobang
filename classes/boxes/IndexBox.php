<?php

class IndexBox extends AbstractBox {
	protected $template = 'menu.tpl';
	
	protected $pageTypes = array('rooms', 'cards', 'roles', 'characters');

	protected function setup() {
		$pages = array();
		foreach ($this->pageTypes as $pageType) {
			$page = PageActionMap::getPageByTypeAndLanguage($pageType);
			if ($page) {
				$pages[] = $page;
			}
		}
		MySmarty::assign('pages', $pages);
	}
}

?>