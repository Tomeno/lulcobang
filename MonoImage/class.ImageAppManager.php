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
 * Basic image manager. Transform input images width selected transformations from types.
 *
 * @author Tomas Majer <majer@monogram.sk>
 * @package MONOGRAM image lib
 */
class ImageAppManager
{
	/**
	 * Image transform types
	 *
	 * @var	array
	 */
	protected $types = array();
	
	/**
	 * Save factory for images
	 *
	 * @var SaveFactory
	 */
	protected $saveFactory;
	
	/**
	 * Constructor
	 *
	 * @param SaveFactory $saveFactory	save factory for images
	 * @return ImageAppManager
	 */
	public function ImageAppManager($saveFactory)
	{
		if (isset($GLOBALS['image_types']) && is_array($GLOBALS['image_types']))
		{
			$this->setTypes($GLOBALS['image_types']);
		}
		$this->saveFactory = $saveFactory;
	}
	
	/**
	 * Set new types for images
	 *
	 * @param array $types
	 */
	public function setTypes($types)
	{
		$this->types = $types;
	}
	
	/**
	 * Check if input type is allowd image type
	 *
	 * @param string $type	image type to check
	 * @return bool			if type is allowed return true, otherwise return false
	 */
	private function allowedType($type)
	{
		return key_exists($type, $this->types);
	}
	
	/**
	 * Transfrom input image width selected type
	 *
	 * @param MonoImage $image	input image for transformation
	 * @param string $type		image type
	 * @return MononImage		new transformed image
	 */
	public function transformImage($image, $type)
	{
		if (!$this->allowedType($type))
		{
			throw new Exception('Not allowed \'type\': \''.$type.'\'');
		}
		$this->saveFactory->setImage($image);
		$this->saveFactory->setType($type);
		$image_path = $this->saveFactory->getNewImagePath($type, $image);
		
		if (isset($this->types[$type]['extension']))
		{
			$new_ext = $this->types[$type]['extension'];
		}
		else 
		{
			$new_ext = '';
		}
		$image_path = $this->changeExtension($image_path, $new_ext);
		
		if (!file_exists($image_path))
		{
			$result = $image->transform($this->types[$type]['transforms'], $image_path);
		}
		else
		{
			$result = new MonoImage($image_path);
		}

		
		
		return $result;
	}
	
	/**
	 * Change output extensions for output file.
	 *
	 * @param string $image_path		source image path
	 * @param string $new_extension		new extension for file, leave '' for no change
	 * @return string					new path to image	
	 */
	private function changeExtension($image_path, $new_extension = '')
	{
		if ($new_extension != '')
		{
			$ext = end(explode('.', $image_path));
			$image_path = substr($image_path, 0, strlen($image_path) - strlen($ext));
			$image_path .= $new_extension; 
		}
		return $image_path;
	}
	
}


?>