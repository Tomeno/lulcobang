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
 * This transformation resize image into input box. 
 * Transform garante the output image can be placed into the box.
 *
 * @author Tomas Majer <majer@monogram.sk>
 * @package MONOGRAM image lib
 */
class BoxTransformFactory extends TransformFactory 
{
	/**
	 * Constructor.
	 *
	 * @param int $width			new image width
	 * @param int $height			new image height
	 * @return BoxTransformFactory	
	 */
	public function BoxTransformFactory($width, $height)
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
				
		list($width, $height) = $this->countSize($old_width, $old_height, $this->width, $this->height);
		
		$newimg = $this->createResource($width, $height);

		imagecopyresampled($newimg, $oldimage, 0, 0, 0, 0, $width, $height, $old_width, $old_height);
		
		imagedestroy($oldimage);
		
		return $newimg;
	}
	
	/**
	 * Count new image size.
	 *
	 * @param int $old_width	old width
	 * @param int $old_height	old height
	 * @param int $max_width	new maximal width
	 * @param int $max_height	new maximal height
	 * @return array			array of two number - width and height
	 */
	public static function countSize($old_width, $old_height, $max_width, $max_height)
	{
		if ($old_width <= $max_width && $old_height <= $max_height)
		{
			return array($old_width, $old_height);
		}
	
		$width_rate		= $max_width  / $old_width;
		$height_rate	= $max_height / $old_height;
		
		if ($width_rate > $height_rate)
		{
			$width	= round($old_width * $height_rate);
			$height	= round($old_height * $height_rate);
		}
		else
		{
			$width	= round($old_width * $width_rate);
			$height	= round($old_height * $width_rate);
		}
		
		return array($width, $height);
	}
	
}

?>
