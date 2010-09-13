<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {link} function plugin
 *
 */
function smarty_block_link($params, $content, &$smarty, &$repeat)
{
	if (!$repeat)
	{
	    if (!isset($params['href']))
		{
			trigger_error("Nebol zadany href pri linke: '" . htmlspecialchars($content) . "'", E_WARNING);
			return $content;
		}
		
		if (!isset($params['title'])) $params['title'] = trim(htmlspecialchars(strip_tags($content)));
		
		$validParams = array(
			'href', 'onclick', 'onmouseover', 'onmouseout', 'class', 'rel', 'title', 'id', 'target'
		);
		
		$attrs = array();
		foreach ($params as $key => $value)
		{
			if (in_array($key, $validParams))
			{
				$attrs[] = $key . '="' . $value . '"';
			}
		}
		
	    return '<a '.implode(' ', $attrs).'>'.$content.'</a>';
	}
}

/* vim: set expandtab: */

?> 