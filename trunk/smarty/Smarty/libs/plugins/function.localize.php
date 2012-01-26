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

	$attr = explode('###', $params);
	return Localize::getMessage($params['key'], $attr);
}

?>