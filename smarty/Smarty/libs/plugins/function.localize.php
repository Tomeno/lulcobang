<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {localize} function plugin
 *
 */

function smarty_function_localize($params, &$smarty) {

	$attrs = array();
	if ($params['attrs']) {
		$attrs = explode('###', $params['attrs']);
	}
	return Localize::getMessage($params['key'], $attrs);
}

?>