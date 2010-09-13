<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty ask_people modifier plugin
 *
 * Type:     modifier
 * Name:     ask_people
 * Purpose:  podla zadaneho cisla modifikuje vetu "pytalo sa x ludi"
 * @author   Michal Lulco <lulco@monogram.sk>
 * @param int
 * @return string
 */
function smarty_modifier_ask_people($int)
{
	switch ($int)
	{
		case 1:
			return 'pýtal sa <strong>' . $int . '</strong> človek';
		case 2:
		case 3:
		case 4:
			return 'pýtali sa <strong>' . $int . '</strong> ľudia';
		default:
			return 'pýtalo sa <strong>' . $int . '</strong> ľudí';
	}
}

?>
