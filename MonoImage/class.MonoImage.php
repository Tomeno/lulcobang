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
 * TODO:
 * doplnit verzie do hlaviciek
 * komentare
 * dokumentacia
 * typo3 plugin - vlozenie obrazku aj so zmensenim
 * captha modul s vyzitim monoimage + pisanie + random shapes
 * cut resizer - smart resizer zmensit a potom kusa odreze
 */

/**
 * Basic Image class. Encapsulate standard operations with images.
 *
 * @author Tomas Majer <majer@monogram.sk>
 * @package MONOGRAM image lib 
 */
class MonoImage
{
	/**
	 * Path to source image.
	 *
	 * @var string
	 */
	private $image_path;
	
	/**
	 * Iformations about image from function 'getimagesize'
	 *
	 * @var array
	 */
	private $image_info;
	
	/**
	 * Extension of actual image from mime type.
	 *
	 * @var string
	 */
	private $resource_image_type;

	protected $fullPath = '';


	public function setFullPath($fullPath)
	{
		$this->fullPath = $fullPath;
	}
	
	/**
	 * Constructor
	 *
	 * @param string $image_path path to source image
	 * @return MonoImage
	 */
	public function MonoImage($image_path, $fullPath = '')
	{
		if ($fullPath) $this->fullPath = $fullPath;
		$this->setImage($image_path);
	}
	
	/**
	 * Transform actual image with all transforms. 
	 * 
	 * @param array $transform_factories 	array of transformations
	 * @param string $output_image			path to output image, or not image will be ouputed to screen
	 */
	public function transform($transform_factories, $output_image = '')
	{
		if ($this->image_info['mime'] == 'image/bmp') throw new Exception('Error: transformation for BMP not supported');

		$resource	= $this->getResource($this->fullPath . $this->image_path);
		$width		= $this->getWidth();
		$height		= $this->getHeight();
		
		if (is_array($transform_factories) && count($transform_factories) > 0)
		{
			foreach ($transform_factories as $transform_factory)
			{
				$resource = $transform_factory->transformImage($resource);
			}
		}
		else
		{
			$resource = $transform_factories->transformImage($resource);
		}
		
		
		if ($output_image == '')
		{
			$this->outputImage($resource, $this->resource_image_type);
			imagedestroy($resource);
			exit();
		}
		else
		{
			$newimage = $this->createImageFromResource($resource, $output_image, $this->fullPath);
			return $newimage;
		}
	}
	
	/**
	 * Set new image.
	 *
	 * @param string $image_path	new path to source image
	 */
	public function setImage($image_path)
	{
		if (file_exists($this->fullPath . $image_path))
		{
			 $this->image_path = $image_path;
			 $this->image_info = getimagesize($this->fullPath . $image_path);
			 if (!is_array($this->image_info))
			 {
			 	throw new Exception('No image input');
			 }
		}
		else
		{
			throw new Exception('Error: File not exists');
		}
	}
	
	/**
	 * Get HTML image element.
	 *
	 * @param string $alt			alternative description for image
	 * @param string $path_prefix	special prefix for image source
	 * @return string				html image element
	 */
	public function htmlTag($alt, $path_prefix = '', $params = array())
	{
		$pars = array();
		foreach ($params as $key => $value) $pars[] = $key . '="' . $value . '"';
		$url = $this->getUrl($path_prefix);
		/*
		if ($path_prefix != '')
		{
			$path_parts = pathinfo($this->image_path);
			return '<img src="'.$path_prefix . $path_parts['basename'].'" '.$this->image_info[3].' alt="'.$alt.'" ' . implode(' ', $pars) . '/>';
		}*/
		return '<img src="' . $url . '" '.$this->image_info[3].' alt="'.$alt.'" ' . implode(' ', $pars) . '/>';
	}
	
	public function getUrl($path_prefix = '')
	{
		if ($path_prefix != '')
		{
			$path_parts = pathinfo($this->image_path);
			return $path_prefix . $path_parts['basename'];
		}
		return $this->image_path;
	}
	
	/**
	 * Returns info array about image.
	 *
	 * @return array	info array about image
	 */
	public function getImageInfo()
	{
		return $this->image_info;
	}
	
	/**
	 * Returns width of image
	 *
	 * @return int	width of image
	 */
	public function getWidth()
	{
		return $this->image_info[0];
	}
	
	/**
	 * Return height of image
	 *
	 * @return int	height of image
	 */
	public function getHeight()
	{
		return $this->image_info[1];
	}
	
	/**
	 * Returns extenstion of image
	 *
	 * @return sting	extension of image (without '.')
	 */
	public function getExtension()
	{
		return end(explode('/',$this->image_info['mime']));
	}
	
	/**
	 * Returns mime type of image
	 *
	 * @return string	mime type of imamge
	 */
	public function getMimeType()
	{
		return $this->image_info['mime'];
	}
	
	/**
	 * Returm image source path
	 *
	 * @return string	image source path
	 */
	public function getImagePath($full = false)
	{
		if ($full) return $this->fullPath . $this->image_path;
		return $this->image_path;
	}
	
	/**
	 * Create and return image resource for gd library.
	 *
	 * @return resource gd image resource of actual image 
	 */
	public function getResource()
	{
		$mime = $this->getMimeType();
		$ext = end(explode('/', $mime));
		
		$this->resource_image_type = $ext;
		
		$result = '';
		
		if ($ext == 'jpeg')
		{
			$result = @imagecreatefromjpeg($this->fullPath . $this->getImagePath());
		}
		else if ($ext == 'gif')
		{
			$result = @imagecreatefromgif($this->fullPath . $this->getImagePath());
		}
		else if ($ext == 'png')
		{
			$result = @imagecreatefrompng($this->fullPath . $this->getImagePath());
		}
		else
		{
			$result = @Exception('Not supported image type');
		}
		
		if (!$result)
		{
			throw new Exception('Cannot create image resource from given image');
		}
		
		return $result;
	}
	
	/**
	 * Create image from gd image resource.
	 *
	 * @param resource 	$resource		gd resource of image
	 * @param string	$output_file	path to output file
	 * @return MonoImage				new MonoImage crated from input resource
	 */
	protected static function createImageFromResource($resource, $output_file, $fullpath = '')
	{
		$output_file = str_replace($fullpath, '', $output_file);

		// get output file extension
		$ext = strtolower(end(explode('.', $fullpath . $output_file)));
		
		if ($ext == 'jpeg' || $ext == 'jpg')
		{
			$result = @imagejpeg($resource, $fullpath . $output_file, 100);
		}
		else if ($ext == 'gif')
		{
			$result = @imagegif($resource, $fullpath . $output_file);
		}
		else if ($ext == 'png')
		{
			$result = @imagepng($resource, $fullpath . $output_file, 0);
		}
		else
		{
			throw new Exception('Bad ouput file format');	
		}

		if (!$result)
		{
			throw new Exception('Output image cannot create from given resource');
		}
		
		return new MonoImage($output_file, $fullpath);
	}
	
	/**
	 * Print image to screen and send headers.
	 *
	 * @param resource $imageres	image gd resource
	 * @param string $ext			image extension
	 */
	protected function outputImage($imageres, $ext)
	{
		header("Content-Type: image/".$ext);
		if ($ext == 'jpeg')
		{
			imagejpeg($imageres,NULL,100);
		}
		else if ($ext == 'gif')
		{
			imagegif($imageres);
		}
		else if ($ext == 'png')
		{
			imagepng($imageres,NULL,0);
		}
		imageInterlace($imageres, 1);
	}
	
	/**
	 * Send actual image to output.
	 */
	public function output()
	{
		$imageres = $this->getResource();
		$this->outputImage($imageres, $this->getExtension());
	}
}

?>
