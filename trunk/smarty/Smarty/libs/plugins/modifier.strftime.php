<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty strftime modifier plugin
 *
 * Type:     modifier<br>
 * Name:     strftime<br>
 * Purpose:  convert timestamp to human redable format
 * @link   
 * @param string
 * @return string
 */
function smarty_modifier_strftime($string, $type=1)
{
	if ($type == 1) {
    	return strftime('%d.%m.%Y', $string);
	} else if ($type == 2) {
		return strftime('%H:%M', $string);
	} else if ($type == 3) {
		return strftime('%d.%m', $string);
	} else if ($type == 4) {
		return strftime('%d.%m.%Y - %H:%M', $string);
	} else if ($type == 5) {
		return strftime('%H:%M', $string);
	} else if ($type == 6) {
		$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
		if (date('d.m.Y')==strftime('%d.%m.%Y', $string))
			return 'DNES';
		elseif (date('d.m.Y',$tomorrow)==strftime('%d.%m.%Y', $string))
			return 'ZAJTRA';
		else		
			return strftime('%d.%m.%Y', $string);
	} else {
		return strftime('%d.%m.%Y', $string);
	}
}
?>
