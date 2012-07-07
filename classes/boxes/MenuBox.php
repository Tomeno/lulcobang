<?php

class MenuBox extends AbstractBox {
	
	protected $template = 'menu.tpl';

	protected $actualAction = NULL;

	protected $pageTypes = array('rooms', 'cards', 'roles', 'characters');

	protected function setup() {
		$pages = array();
		$pageTypeRepository = new PageTypeRepository();
		$hasSelected = FALSE;
		foreach ($this->pageTypes as $pageTypeAlias) {
			$page = PageActionMap::getPageByTypeAndLanguage($pageTypeAlias);
			
			if ($page) {
				if ($hasSelected === FALSE) {
					$pageType = $pageTypeRepository->getOneByAlias($pageTypeAlias);
					if ($pageType['action'] == $this->actualAction) {
						$page['sel'] = TRUE;
						$hasSelected = TRUE;
					}
				}
				$pages[] = $page;
			}
		}
		MySmarty::assign('pages', $pages);
	}

	public function setActualAction($action) {
		$this->actualAction = $action;
	}
}

?>