<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Jozef Spisiak <spisiak@monogram.sk>
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
 * This transformation resize image into input InnerMax. 
 * Transform garante the output image can be placed into the InnerMax.
 *
 * @author Jozef Spisiak <spisiak@monogram.sk>
 * @package MONOGRAM image lib
 */
class InnerMaxTransformFactory extends TransformFactory 
{
	/**
	 * Constructor.
	 *
	 * @param int $width			new image width
	 * @param int $height			new image height
	 * @return InnerMaxTransformFactory	
	 */
	public function InnerMaxTransformFactory($width, $height)
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
		
		if ($width == $old_width) return $oldimage;
		$newimg = $this->createResource($width, $height);
		if ($old_width>$old_height)
		{
		  $k = $old_height/$height;
		  imagecopyresampled($newimg, $oldimage, 0, 0, ($old_width-$k*$width)/4, 0, $width, $height, $old_width-($old_width-$k*$width)/2, $old_height);
		}
		else
		{
		  $k = $old_width/$width;
		  imagecopyresampled($newimg, $oldimage, 0, 0, 0, ($old_height-$k*$height)/4, $width, $height, $old_width, $old_height-($old_height-$k*$height)/2);
		}
		 
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
	  return array($max_width,$max_height);
	}
	
}

?>