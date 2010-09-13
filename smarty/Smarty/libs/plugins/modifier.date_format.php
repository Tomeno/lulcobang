<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Include the {@link shared.make_timestamp.php} plugin
 */
require_once $smarty->_get_plugin_filepath('shared', 'make_timestamp');
/**
 * Smarty date_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     date_format<br>
 * Purpose:  format datestamps via strftime<br>
 * Input:<br>
 *         - string: input date string
 *         - format: strftime format for output
 *         - default_date: default date if $string is empty
 * @link http://smarty.php.net/manual/en/language.modifier.date.format.php
 *          date_format (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param string
 * @param string
 * @return string|void
 * @uses smarty_make_timestamp()
 */
function smarty_modifier_date_format($string, $format = '%b %e, %Y', $default_date = '')
{ 
	if ($string != '') {
		$timestamp = smarty_make_timestamp($string);
    } elseif ($default_date != '') {
        $timestamp = smarty_make_timestamp($default_date);
    } else {
        return;
    }
    if (DIRECTORY_SEPARATOR == '\\' || DIRECTORY_SEPARATOR == '/') {
        $_win_from = array('%D',       '%h', '%n', '%r',          '%R',    '%t', '%T');
        $_win_to   = array('%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S');
        if (strpos($format, '%e') !== false) {
            $_win_from[] = '%e';
            $_win_to[]   = sprintf('%\' 2d', date('j', $timestamp));
        }
        if (strpos($format, '%m') !== false)
        {
        	$_win_from[] = '%m';
            $_win_to[]   = sprintf('%\' 2d', date('n', $timestamp));
        }
        if (strpos($format, '%l') !== false) {
            $_win_from[] = '%l';
            $_win_to[]   = sprintf('%\' 2d', date('h', $timestamp));
        }
        
        // slovenske nazvy mesiacov
        if (strpos($format, '%F') !== false) {
			$months = DobraKrajinaPageTime::$months;
			$_win_from[] = '%F';
			$_win_to[]   = sprintf('%\' s', $months[date('m', $timestamp)-1]);
        }
        
        // slovenske nazvy dni
        if (strpos($format, '%N') !== false) {
			$days = DobraKrajinaPageTime::$weekDays;
			$_win_from[] = '%N';
			$_win_to[]   = sprintf('%\' s', $days[date('N', $timestamp)-1]);
        }
        
        $format = str_replace($_win_from, $_win_to, $format);
    }
    return strftime($format, $timestamp);
}

/* vim: set expandtab: */

?>
