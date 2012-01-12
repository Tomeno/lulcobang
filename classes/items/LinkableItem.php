<?php

abstract class LinkableItem extends Item {

	public function getUrl($language = NULL) {
		$page = $this->getPageAlias($language);
		$itemAlias = $this->getItemAlias();
		$url = PageActionMap::createUrl(array($page['alias'], $itemAlias));
		return $url;
	}

	protected function getPageAlias($language = NULL) {
		$pageType = $this->getPageType();
		$page = PageActionMap::getPageByTypeAndLanguage($pageType, $language);

		return $page;
	}

	abstract protected function getPageType();

	abstract protected function getItemAlias();
}

?>