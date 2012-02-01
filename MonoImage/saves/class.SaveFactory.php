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
 * Basic abstract factory for saving images
 *
 * @author Tomas Majer <majer@monogram.sk>
 * @package MONOGRAM image lib
 */
abstract class SaveFactory
{
	/**
	 * Basic abstract method for generate new image path width apply specific save factory rules.
	 *
	 * @param string $type		type of image
	 * @param MonoImage $image	source image
	 */
	abstract function getNewImagePath($type = '', $image = null);
	
	/**
	 * Source image.
	 *
	 * @var MonoImage
	 */
	protected $image;
		
	/**
	 * Type of transformation
	 *
	 * @var string
	 */
	protected $type;
	
	/**
	 * Default permissions for new folders
	 *
	 * @var string
	 */
	protected $defaultPermissions = 0777;
	
	/**
	 * Set new source image
	 *
	 * @param MonoImage $image
	 */
	public function setImage($image)
	{
		if ($image != null)
		{
			$this->image = $image;
		}
	}
	
	/**
	 * Set new type
	 *
	 * @param string $type
	 */
	public function setType($type)
	{
		if ($type != '')
		{
			$this->type = $type;
		}
	}
	
	/**
	 * Create new directory
	 *
	 * @param string $dirpath	new directory path
	 * @return string			return new directory path
	 */
	protected function makedir($dirpath)
	{
		 if (!is_dir($dirpath))
		 {
		 	$status = mkdir($dirpath);
			chmod ($dirpath, $this->defaultPermissions);
		 	if (!$status)
		 	{
		 		throw new Exception('Cannot create directory :\''.$dirpath.'\'');
		 	}
		 }
		 return $dirpath;
	}
}

?>
