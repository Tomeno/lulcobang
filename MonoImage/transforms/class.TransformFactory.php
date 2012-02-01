<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Tomas Majer <majer@monogram.sk>
*  All rights reserved
*
*  This script is part of the MONOGRAM image library. The MONOGRAM image library is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Basic transform abstract class
 * Implements a few funcions for transformations.
 * 
 * @author Tomas Majer <majer@monogram.sk>
 * @package MONOGRAM image lib
 */
abstract class TransformFactory
{
	/**
	 * Basic metod for transform input image.
	 *
	 * @param MonoImage $oldimage
	 */
	abstract public function transformImage($oldimage);
	
	/**
	 * Atialiasing output image.
	 *
	 * @var bool
	 */
	protected $antialias = false;
	
	/**
	 * Width of ouput image
	 *
	 * @var int
	 */
	protected $width;
	
	/**
	 * Height of output image
	 *
	 * @var int
	 */
	protected $height;
	
	/**
	 * Set new output width
	 *
	 * @param int $new_width	new output width
	 */
	protected function setWidth($new_width)
	{
		if (is_numeric($new_width) && $new_width > 0)
		{
			$this->width = $new_width;
		}
	}
	
	/**
	 * Set new output height
	 *
	 * @param int $new_height	new output height
	 */
	protected function setHeight($new_height)
	{
		if (is_numeric($new_height) && $new_height > 0)
		{
			$this->height = $new_height;
		}
	}
	
	/**
	 * Create new gd image resource with selected size
	 *
	 * @param int $width	width of new resource
	 * @param int $height	height of new resource
	 * @return resource		new gd image resource
	 */
	protected function createResource($width, $height)
	{
		$res = imagecreatetruecolor($width, $height);
	//	$this->antialias($res);
		return $res;
	}
	
	/**
	 * Seting antialiasing for ouput image
	 *
	 * @param bool $antialias
	 */
	public function setAntialias($antialias)
	{
		if (is_bool($antialias))
		{
			$this->antialias = $antialias;
		}
	}
	
	/**
	 * Enable of disablene atialiasign for given resource
	 *
	 * @param resource $resource	input resource
	 */	
	protected function antialias(&$resource)
	{
		if ($this->antialias)
		{
			$alias = true;
			imageantialias($resource, $alias);
		}
	}
	
	/**
	 * Returns actual input image width
	 *
	 * @return int
	 */
	public function getWidth()
	{
		return $this->width;
	}
	
	/**
	 * Returns actual input image height
	 *
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}
}

?>
