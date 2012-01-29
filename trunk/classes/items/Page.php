<?php

class Page extends Item {

	protected $pageType = NULL;

	public function getUrl() {
		return PageActionMap::createUrl($this['alias']);
	}

	public function getPageTypeObject() {
		if ($this->pageType === NULL) {
			$pageTypeRepository = new PageTypeRepository();
			$this->pageType = $pageTypeRepository->getOneById($this['page_type']);
		}
		return $this->pageType;
	}
}

?>