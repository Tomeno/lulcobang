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

			$aliases = array();
			if ($actualPage) {
				$pageTypeRepository = new PageTypeRepository();
				$pageType = $pageTypeRepository->getOneById($actualPage['page_type']);

				$page = PageActionMap::getPageByTypeAndLanguage($pageType['alias'], $this['shortcut']);
				$aliases[] = $page['alias'];
			} else {
				$aliases[] = $action;
			}

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