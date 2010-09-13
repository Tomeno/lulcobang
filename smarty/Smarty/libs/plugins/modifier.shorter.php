<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty string_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     shorter<br>
 * Purpose:  shorter strings to lenght
 * @link http://smarty.php.net/manual/en/language.modifier.string.format.php
 *          string_format (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_shorter($string, $lenght = 30)
{
	return UTF8::substr($string, 0, $lenght);
	
	if (strlen($string) > $lenght)
	{
		return ParseHTMLText::shortenText($string, $lenght);
	}
	return $string;
}

?>
