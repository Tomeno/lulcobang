<?php

class StaticPageBox extends AbstractBox {

	protected $template = 'static-page.tpl';

	protected function setup() {
		$pageTypeRepository = new PageTypeRepository(TRUE);
		$pageType = $pageTypeRepository->getOneByAlias(Utils::get('action'));
		
		$pageRepository = new PageRepository(TRUE);
		$page = $pageRepository->getOneByPageType($pageType['id']);

		MySmarty::assign('page', $page);
	}
}

?>