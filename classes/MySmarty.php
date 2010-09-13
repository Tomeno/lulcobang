<?php

class MySmarty {
	
	public function init() {
		if ($GLOBALS['smarty'] === null) {
			require_once(dirname(__FILE__) . '/../smarty/Smarty/libs/Smarty.class.php');
			
			// vytvorime nove smarty
			$smarty = new Smarty;
			$smarty->template_dir = dirname(__FILE__) . '/../templates/';
		 	$smarty->compile_dir = dirname(__FILE__) . '/../tmp/smarty-dir/templates_c/';
		 	$smarty->cache_dir = dirname(__FILE__) . '/../tmp/smarty-dir/cache/';
		 	$smarty->config_dir = dirname(__FILE__) . '/../tmp/smarty-dir/configs/';
		 	$GLOBALS['smarty'] = $smarty;
		}
	}
	
	public static function assign($key, $value) {
		self::init();
		$GLOBALS['smarty']->assign($key, $value);
	}
	
	public static function fetch($template) {
		self::init();
		if ($GLOBALS['smarty']->template_exists($template)) {
			return $GLOBALS['smarty']->fetch($template);
		}
		return '<!-- template ' . $template . ' not found -->';
	}
}

?>