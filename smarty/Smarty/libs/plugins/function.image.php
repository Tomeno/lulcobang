<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {image} function plugin
 *
 */

function smarty_function_image($params, &$smarty)
{
	if (!isset($params['src'])) {
		return '<!-- image not set -->';
	} else {
		$filename = dirname(__FILE__) . '/../../../../' . $params['src'];
		if (file_exists($filename)) {
			$img = '<img src="' . $params['src'] . '"';
			if ($params['alt']) {
				$img .= ' alt="' . $params['alt'] . '"';
			} else {
				$img .= ' alt=""';
			}

			if ($params['class']) {
				$img .= ' class="' . $params['class'] .'"';
			}

			if ($params['width']) {
				$img .= ' width="' . $params['width'] . '"';
			}
			if ($params['height']) {
				$img .= ' height="' . $params['height'] . '"';
			}

			$img .= ' />';
			return $img;
		} else {
			return '<!-- image ' . $params['src'] . ' not found -->';
		}
	}
}

/* vim: set expandtab: */

?> 