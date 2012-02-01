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

require_once(dirname(__FILE__).'/class.TransformFactory.php');

/**
 * Dummy image transform.
 * Create new image width selected width and height. Only dummy resize input image.
 *
 * @author Tomas Majer <majer@monogram.sk>
 * @package MONOGRAM image lib
 */
class DummyTransformFactory extends TransformFactory 
{
	/**
	 * Constructor
	 *
	 * @param int $width				new image width
	 * @param int $height				new image height
	 * @return DummyTransformFactory
	 */
	public function DummyTransformFactory($width, $height)
	{
		$this->setWidth($width);
		$this->setHeight($height);
	}
	
	/**
	 * Transfrom input resource to new resource with new width and height.
	 *
	 * @param resource $oldimage	input image gd resource
	 * @return resource				new image resource 
	 */
	public function transformImage($oldimage)
	{
		$old_width = imagesx($oldimage);
		$old_height = imagesy($oldimage);
		
		$newimg = $this->createResource($this->width, $this->height);

		imagecopyresampled($newimg, $oldimage, 0, 0, 0, 0, $this->width, $this->height, $old_width, $old_height);
		
		imagedestroy($oldimage);
		
		return $newimg;
	}
		
}

?>