<?php

/**
 * abstract class for linkable items
 *
 * @author Michal Lulco <michal.lulco@gmail.com>
 */
abstract class LinkableItem extends Item {

	/**
	 * getter for url of Item
	 *
	 * @param	string	$language	shortcut of Language
	 * @return	string
	 */
	public function getUrl($language = NULL) {
		$page = $this->getPageAlias($language);
		$itemAlias = $this->getItemAlias();
		$url = PageActionMap::createUrl(array($page['alias'], $itemAlias), $language);
		return $url;
	}

	/**
	 * page alias for Item - first part of url after language
	 *
	 * @param	string	$language	shortcut of Language
	 * @return	Page
	 */
	protected function getPageAlias($language = NULL) {
		$pageType = $this->getPageType();
		$page = PageActionMap::getPageByTypeAndLanguage($pageType, $language);

		return $page;
	}

	/**
	 * getter for page type - for the part of url
	 *
	 * @return	string
	 */
	abstract protected function getPageType();

	/**
	 * getter for item alias - last part of url
	 *
	 * @return	string
	 */
	abstract protected function getItemAlias();
}

?>