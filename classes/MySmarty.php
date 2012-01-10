<?php

/**
 * smarty
 */
class MySmarty {
	
	protected static $smarty = NULL;
	
	/**
	 * initialization of Smarty
	 */
	protected function init() {
		if (self::$smarty === NULL) {
			require_once(dirname(__FILE__) . '/../smarty/Smarty/libs/Smarty.class.php');
			
			// vytvorime nove smarty
			$smarty = new Smarty;
			$smarty->template_dir = $GLOBALS['smartyDir'];
			$templatesC = dirname(__FILE__) . '/../tmp/smarty-dir/templates_c/';
			if (!file_exists($templatesC)) {
				mkdir($templatesC);
			}
			$templatesCache = $templatesC . $GLOBALS['smartyTempCacheDir'];
			if (!file_exists($templatesCache)) {
				mkdir($templatesCache);
			}
		 	$smarty->compile_dir = $templatesCache;
		 	$smarty->cache_dir = dirname(__FILE__) . '/../tmp/smarty-dir/cache/';
		 	$smarty->config_dir = dirname(__FILE__) . '/../tmp/smarty-dir/configs/';
		 	self::$smarty = $smarty;
		}
	}

	/**
	 * assigning the value for template
	 *
	 * @param	string	$key
	 * @param	mixed	$value
	 * @return	void
	 */
	public static function assign($key, $value) {
		self::init();
		self::$smarty->assign($key, $value);
	}

	/**
	 * fetching the template
	 *
	 * @param	string	$template
	 * @return	string
	 */
	public static function fetch($template) {
		self::init();
		if (self::$smarty->template_exists($template)) {
			return self::$smarty->fetch($template);
		}
		return '<!-- template ' . $template . ' not found -->';
	}
}

?>