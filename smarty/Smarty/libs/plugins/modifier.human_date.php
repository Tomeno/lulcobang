<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty homan date modifier plugin
 *
 * Type:     modifier<br>
 * Name:     default<br>
 * Purpose:  designate default value for empty variables
 * @link http://smarty.php.net/manual/en/language.modifier.default.php
 *          default (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_human_date($string, $type = 1)
{
	if ($type == 1)
	{
		return MedialneTimeFormat::formatType1($string);
	}
	else if ($type == 2)
	{
		return MedialneTimeFormat::formatType2($string);
	}

    //return HumanDate::format($string, $format);
}

/* vim: set expandtab: */

?> 