<?php

abstract class AbstractBox {

	protected $template = '';

	protected static $seoAdded = FALSE;

	public function render() {
		$this->addSeo();
		$this->setup();
		return MySmarty::fetch($this->template);
	}

	protected function addSeo() {
		$action = Utils::get('action');
		$pageRepository = new PageRepository();
		$page = $pageRepository->getOneByAlias($action);

		if (self::$seoAdded === FALSE) {
			BangSeo::addTitlePart($page['title']);
			BangSeo::setDescription($page['meta_description']);
			BangSeo::addContentForKeywords($page['meta_keywords'], BangSeo::MEDIUM_PRIORITY);
			self::$seoAdded = TRUE;
		}
	}

	abstract protected function setup();
}

?>