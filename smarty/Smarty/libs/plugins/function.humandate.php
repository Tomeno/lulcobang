<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {humandate} function plugin
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_humandate($params, &$smarty)
{
	$dateFormatVar = AbstractBox::HUMAN_DATE_VAR;
	if (isset($params[$dateFormatVar]))
	{
		$rulesGroup = $params[$dateFormatVar];
	}
	elseif ($smarty->_tpl_vars[$dateFormatVar])
	{
		$rulesGroup = $smarty->_tpl_vars[$dateFormatVar];
	}
	else
	{
		// hacked
		return date('j. n. Y', $params['value']);
		throw new Exception('Nenasiel sa format pre datum');
	}
	$humanDate = new MyHumanDate($rulesGroup);
	return $humanDate->formatDate($params['value']); 
}
/* vim: set expandtab: */

?>
