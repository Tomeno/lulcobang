<?php

class Language extends Item {
	public function getUrl() {
		return PageActionMap::createUrl(array(), $this['shortcut']);
	}

	public function getCorrespondingUrl() {
		$action = Utils::get('action');
		if ($action) {
			$pageRepository = new PageRepository();
			$actualPage = $pageRepository->getOneByAlias($action);

			$pageTypeRepository = new PageTypeRepository();
			$pageType = $pageTypeRepository->getOneById($actualPage['page_type']);

			$aliases = array();
			$page = PageActionMap::getPageByTypeAndLanguage($pageType['alias'], $this['shortcut']);
			$aliases[] = $page['alias'];

			if (Utils::get('identifier')) {
				$aliases[] = Utils::get('identifier');
			}

			return PageActionMap::createUrl($aliases, $this['shortcut']);
		} else {
			return $this->getUrl();
		}
	}
}

?>