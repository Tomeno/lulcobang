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
		$baseDir = dirname(__FILE__) . '/../../../../';
		$filename =  $baseDir . $params['src'];
		if (file_exists($filename)) {

			$newImageBase = $baseDir . 'tmp/media/' . $params['width'] . '-' . $params['height'] . '/';
			$folders = explode('/', $params['src']);
			array_pop($folders);
			$folder = $newImageBase . implode('/', $folders);
			if (!file_exists($folder)) {
				mkdir($folder, 0777, TRUE);
			}

			$newPath = $newImageBase . $params['src'];
			if (!file_exists($newPath)) {
				$image = new MonoImage($filename);
				$image->transform(new TrimTransformFactory($params['width'], $params['height']), $newPath);
			}

			$img = '<img src="' . str_replace($baseDir, '', $newPath) . '"';
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