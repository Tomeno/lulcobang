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
function smarty_modifier_linkify($string)
{
	return preg_replace('@https?://(.*?)(/(.*?))?(?=\s|$)@u', '<a href="\0" onclick=\'window.open("\0", "_blank"); return false;\'>\1</a>', $string);
}

?> 