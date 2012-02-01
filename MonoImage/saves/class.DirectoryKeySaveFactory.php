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

require_once(dirname(__FILE__).'/class.SaveFactory.php');

/**
 * Save images in separete folders. Each folder has name from type.
 *
 * @author Tomas Majer <majer@monogram.sk>
 * @package MONOGRAM image lib
 */
class DirectoryKeySaveFactory extends SaveFactory 
{
	/**
	 * Get new path to image. Create new directory if not exists. 
	 * Directories has name of type.
	 *
	 * @param string $type			image type
	 * @param MonoImage $image		source image
	 * @return string				new path to image
	 */
	function getNewImagePath($type = '', $image = null)
	{
		$this->setType($type);
		$this->setImage($image);
		
		$img_path = $this->image->getImagePath(true);
		
		$directory = dirname($img_path).'/';
		$file = basename($img_path);
		
		$new_directory = $directory.$type.'/';
		$this->makedir($new_directory);
		
		$new_file = $new_directory.$file;
		
		return $new_file;
	}
}

?>