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
 * This transformation resize image. It resize it by ...
 *
 * @author Tomas Majer <majer@monogram.sk>
 * @package MONOGRAM image lib
 */
class MaxTransformFactory extends TransformFactory
{
	/**
	 * Constructor.
	 * 
	 * @param int $width	width of output image
	 * @param int $height	height of output image
	 * @return MaxTransformFactory
	 */
	public function MaxTransformFactory($width, $height)
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
		
		if ($old_width > $old_height) 
		{
			$new_width = $this->width;
			$new_height = floor($old_height * ($this->width / $old_width));
        } 
        else if ($old_width < $old_height) 
        {
			$new_height = $this->height;
			$new_width = floor($old_width * ($this->height / $old_height));
        } 
        else 
        {
			$new_height = $this->height;
			$new_width = $this->width;
        }
        
        $newimg = $this->createResource($new_width, $new_height);
        
		imagecopyresampled($newimg, $oldimage, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
		
		imagedestroy($oldimage);
		
		return $newimg;
	}
}

?>