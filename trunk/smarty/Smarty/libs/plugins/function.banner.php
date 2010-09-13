<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {banner} function plugin
 *
 */

function smarty_function_banner($params, &$smarty)
{
    if (!isset($params['type'])) throw new InvalidArgumentException("Nebol zadany 'type' pre baner");

	return OpenXBanner::getBanner($params['type']);
}

/* vim: set expandtab: */

?>