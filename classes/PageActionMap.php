<?php

class PageActionMap {

	public static function getActionByPageAndLanguage($alias, $lang = NULL) {
		$language = Utils::getLanguage($lang);

		if ($language) {
			if ($alias == '') {
				return 'index';
			} else {
				$pageRepository = new PageRepository(TRUE);
				$page = $pageRepository->getOneByLanguageAndAlias($language['id'], $alias);

				if ($page) {
					$pageType = $page->getPageTypeObject();
					if ($pageType) {
						$action = $pageType['action'];
						if ($action) {
							return $action;
						} else {
							throw new Exception('Action not found', 1326407427);
						}
					} else {
						throw new Exception('Page type not found', 1326407401);
					}
				} else {
					return 'pageNotFound';
				}
			}
		} else {
			throw new Exception('Language ' . $lang . ' not found', 1326403880);
		}
	}

	public static function getPageByTypeAndLanguage($type, $lang = NULL) {
		$language = Utils::getLanguage($lang);
		if ($language) {
			$pageTypeRepository = new PageTypeRepository(TRUE);
			$pageType = $pageTypeRepository->getOneByAlias($type);

			$pageRepository = new PageRepository(TRUE);
			$page = $pageRepository->getOneByLanguageAndPageType($language['id'], $pageType['id']);

			if ($page) {
				return $page;
			} else {
				throw new Exception('Page with type ' . $pageType['title'] . ' and language ' . $language['title'] . ' doesn\'t exist', 1326403622);
			}
		} else {
			throw new Exception('Language ' . $lang . ' not found', 1326403989);
		}
	}

	public static function createUrl($aliases = array(), $lang = NULL) {
		if (!is_array($aliases)) {
			$aliases = array($aliases);
		}

		if ($lang === NULL) {
			$language = Utils::getLanguage();
			$lang = $language['shortcut'];
		}

		$url = $lang;
		foreach ($aliases as $alias) {
			$url .= '/' . $alias;
		}
		$url .= '.html';
		return $url;
	}
}

?>