<?php

/**
 * class for setting page title, meta description and meta keywords
 *
 * @author	Michal Lulco <michal.lulco@gmail.com>
 */
class BangSeo {

	const LOW_PRIORITY = 1;
	
	const MEDIUM_PRIORITY = 2;
	
	const HIGH_PRIORITY = 3;

	protected static $titleParts = array();

	protected static $description = '';

	protected static $keywords = array();

	protected static $separators = array(',','.',';','!','?',':','\'','"','{','}','[',']','(',')');

	protected static $maxKeywords = 20;

	protected static $minKeywodrLength = 4;

	/**
	 * adds text to title
	 *
	 * @param	string	$titlePart
	 * @return	void
	 */
	public static function addTitlePart($titlePart) {
		if ($titlePart) {
			self::$titleParts[] = $titlePart;
			self::addContentForKeywords($titlePart, self::HIGH_PRIORITY);
		}
	}

	/**
	 * gets title for header
	 *
	 * @param	string	$separator
	 * @return	string
	 */
	public static function getTitle($separator = ' | ') {
		return implode($separator, array_reverse(self::$titleParts));
	}

	/**
	 * sets description of actual page
	 *
	 * @param	string	$description
	 * @return	void
	 */
	public static function setDescription($description) {
		$description = str_replace('"', "'", $description);
		self::$description = $description;
		self::addContentForKeywords($description, self::MEDIUM_PRIORITY);
	}

	/**
	 * gets the description of actual page for meta tag
	 *
	 * @return	string
	 */
	public static function getDescription() {
		return self::$description;
	}

	/**
	 * adds content to generate keywords
	 *
	 * @param	string	$content
	 * @param	integer	$priority
	 * @return	void
	 */
	public static function addContentForKeywords($content, $priority) {
		$content = addslashes(strip_tags($content));
		$words = self::parseContent($content);

		foreach ($words as $word) {
			self::addKeyword($word, $priority);
		}
	}

	/**
	 * creates array of words from content
	 *
	 * @param	string	$content
	 * @return	array
	 */
	protected static function parseContent($content) {
		$content = strip_tags($content);
		$content = mb_strtolower($content, 'UTF-8');
		$content = str_ireplace(self::$separators, ' ', $content);
		$words = explode(' ', $content);

		return $words;
	}

	/**
	 * adds keyword to array and raise its priority
	 * 
	 * @param	string	$keyword
	 * @param	integer	$priority
	 * @return	void
	 */
	protected static function addKeyword($keyword, $priority) {
		if (mb_strlen($keyword, 'UTF-8') >= self::$minKeywodrLength) {
			self::$keywords[$keyword] += $priority;
		}
	}

	/**
	 * gets keywords of actual page for meta tag
	 * 
	 * @return	string
	 */
	public static function getKeywords() {
		arsort(self::$keywords);
		$allKeywords = array_keys(self::$keywords);

		$keywords = array();

		foreach ($allKeywords as $keyword) {
			$keywords[] = $keyword;
			if (count($keywords) >= self::$maxKeywords) {
				break;
			}
		}

		return implode(', ', $keywords);
	}
}

?>