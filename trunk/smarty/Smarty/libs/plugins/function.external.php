<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {external} function plugin
 *
 */

function smarty_function_external($params, &$smarty)
{
    if (!isset($params['key'])) throw new InvalidArgumentException("Nebol zadany 'key' pre nalodovanie kniznice");
	if (!isset($params['type'])) throw new InvalidArgumentException("Nebol zadany 'type' pre nalodovanie kniznice");
	if (!in_array($params['type'], array('js', 'css'))) throw new InvalidArgumentException("Nebol zadany spravny 'type' (js | css) pre nalodovanie kniznice");
	if (!isset($params['key'])) return '';

	if ($params['type'] == 'js')
	{
		External::addJS($params['key']);
	}
	else
	{
		External::addCSS($params['key']);
	}

	return '';
}

/* vim: set expandtab: */

?>