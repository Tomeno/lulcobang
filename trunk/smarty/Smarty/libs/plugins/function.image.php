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
	if (!isset($params['type'])) throw new InvalidArgumentException("Nebol zadany 'type' pre obrazok");
	if (!isset($params['path']) || $params['path'] == '') return '';
	
	// doplnene zatial kvoli projektom, uvidime co bude dalej
	if ($params['uploads'] == 'false')
	{
		$path = $params['path'];
	}
	else
	{
		$path = 'uploads/tx_media/' . $params['path'];
	}
	
	try {
		$image = new MonoImage($path);
	
		$image = $GLOBALS['image_manager']->transformImage($image, $params['type']);
	}
	catch (Exception $e)
	{
		return '';
	}
	$attrs = array();
	if (isset($params['class'])) $attrs[] = 'class="' . $params['class'] . '"';
	if (isset($params['width'])) $attrs[] = 'width="' . $params['width'] . 'px"';
	if (isset($params['height'])) $attrs[] = 'height="' . $params['height'] . 'px"';
	
    return '<img src="' . $image->getImagePath() . '" alt="' . (isset($params['alt']) ? $params['alt'] : '') . '" ' . implode(' ', $attrs) . '/>';
}

/* vim: set expandtab: */

?> 