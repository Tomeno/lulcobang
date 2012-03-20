<?php

class StaticPageBox extends AbstractBox {

	protected $template = 'static-page.tpl';

	protected function setup() {
		$pageTypeRepository = new PageTypeRepository();
		$pageType = $pageTypeRepository->getOneByAlias(Utils::get('action'));

		$pageRepository = new PageRepository();
		$page = $pageRepository->getOneByPageType($pageType['id']);

		MySmarty::assign('page', $page);
	}
}

?>