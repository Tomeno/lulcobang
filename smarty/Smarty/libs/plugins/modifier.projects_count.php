<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty projects_count modifier plugin
 *
 * Type:     modifier
 * Name:     projects_count
 * Purpose:  podla zadaneho cisla modifikuje slovo "projekt"
 * @author   Michal Lulco <lulco@monogram.sk>
 * @param int
 * @return string
 */
function smarty_modifier_projects_count($int)
{
	switch ($int)
	{
		case 1:
			return '<strong>' . $int . '</strong> projekt';
		case 2:
		case 3:
		case 4:
			return '<strong>' . $int . '</strong> projekty';
		default:
			return '<strong>' . $int . '</strong> projektov';
	}
}

?>
