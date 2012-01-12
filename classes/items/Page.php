<?php

class Page extends Item {

	protected $pageType = NULL;

	public function getPageType() {
		if ($this->pageType === NULL) {
			$pageTypeRepository = new PageTypeRepository();
			$this->pageType = $pageTypeRepository->getOneById($this['page_type']);
		}
		return $this->pageType;
	}
}

?>