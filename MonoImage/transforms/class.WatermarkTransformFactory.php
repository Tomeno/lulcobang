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
 * This transformation apply to image transparent layout width new image.
 *
 * @author Tomas Majer <majer@monogram.sk>
 * @package MONOGRAM image lib
 */
class WatermarkTransformFactory extends TransformFactory 
{
	/**
	 * Watermak image.
	 *
	 * @var MonoImage
	 */
	private $transparent_image;
	
	/**
	 * Top left position of watermark
	 */
	const TOP_LEFT = 'top-left';
	
	/**
	 * Top center position of watermak
	 */
	const TOP_CENTER = 'top-center';
	
	/**
	 * Top right position of watermak
	 */
	const TOP_RIGHT = 'top-right';
	
	/**
	 * Center left position of watermak
	 */
	const CENTER_LEFT = 'center-left';
	
	/**
	 * Center position of watermak
	 */
	const CENTER_CENTER = 'center-center';
	
	/**
	 * Center right position of watermak
	 */
	const CENTER_RIGHT = 'center-right';
	
	/**
	 * Bottom left position of watermak
	 */
	const BOTTOM_LEFT = 'bottom-left';
	
	/**
	 * Bottom center position of watermak
	 */
	const BOTTOM_CENTER = 'bottom-center';
	
	/**
	 * Bottom right position of watermak
	 */
	const BOTTOM_RIGHT = 'bottom-right';
	
	/**
	 * Resize watermark to whole source image
	 */
	const WHOLE = 'whole';
	
	/**
	 * Actual position of watermark
	 *
	 * @var string
	 */
	private $position = 'whole';
	
	/**
	 * Alpha value -> 100 = no alpha
	 * 
	 * @var int
	 */
	private $alpha = 100;
	
	/**
	 * Contructor
	 *
	 * @param int 		$width					result width
	 * @param int		$height					result height
	 * @param MonoImage $transparent_image		transparent image -> watermark
	 * @param int 		$alpha					alpha value
	 * @param string	$position				position of watermark
	 * @return TransparentImageTransformFactory
	 */
	public function WatermarkTransformFactory($transparent_image, $alpha = 100, $position = 'whole')
	{
		//$this->setWidth($width);
		//$this->setHeight($height);
		$this->transparent_image = $transparent_image;
		$this->position = $position;
		$this->alpha = $alpha;
	}
	
	/**
	 * Apply watermark to image.
	 *
	 * @param resource $oldimage	input image gd resource
	 * @return resource				new image resource 
	 */
	public function transformImage($oldimage)
	{
		$this->width = imagesx($oldimage);
		$this->height = imagesy($oldimage);
		
		// prepare new result image resource
		$newimg = $this->createResource($this->width, $this->height);
		imagecopyresampled($newimg, $oldimage, 0, 0, 0, 0, $this->width, $this->height, $this->width, $this->height);
		imagedestroy($oldimage);
		
		imagealphablending($newimg, true);
				
		$new_tr = $this->createLayerResource($this->width, $this->height);
		
		$this->mergeResources($newimg, $new_tr);
		
		return $newimg;
	}
		
	/**
	 * Create new image resource from watermark image - int whole position resize image
	 *
	 * @param int $old_width		width
	 * @param int $old_height		height
	 * @return resource				new image resource for watermark
	 */
	public function createLayerResource($old_width, $old_height)
	{
		if ($this->position == WatermarkTransformFactory::WHOLE)
		{
			//$new_tr = $this->transparent_image->getResource();
			
			// prepare transparent resized resource
			$tr_resource = $this->transparent_image->getResource();
			$new_tr = $this->createResource($this->width, $this->height);
			
			
			imagealphablending($new_tr, false);
			imagesavealpha($new_tr, true);
			
			if ($this->transparent_image->getExtension() == 'png')
			{
				imagecopyresampled($new_tr, $tr_resource, 0, 0, 0, 0, $this->width, $this->height, $this->transparent_image->getWidth(), $this->transparent_image->getHeight());
			}
			else if ($this->transparent_image->getExtension() == 'gif')
			{
				imagecolortransparent($new_tr, imagecolorallocate($new_tr, 0, 0, 0));
				imagecopyresized($new_tr, $tr_resource, 0, 0, 0, 0, $this->width, $this->height, $this->transparent_image->getWidth(), $this->transparent_image->getHeight());
			}
			else 
			{
				throw new Exception('Transprent not supported for file '.$this->transparent_image->getExtension());
			}
			imagedestroy($tr_resource);
			//$new_tr = $tr_resource;
			
		}
		else
		{
			$new_tr = $this->transparent_image->getResource();
		}
		
		return $new_tr;
	}
	
	/**
	 * Merge two resources.
	 *
	 * @param resource $newimg		orginal image resource
	 * @param resource $new_tr		watermark image resource
	 * @return resource				new orginal image resource width watermark
	 */
	private function mergeResources(&$newimg, &$new_tr)
	{
		$t_width = imagesx($new_tr);
		$t_height = imagesy($new_tr);
		
		imagesavealpha($new_tr, 1);
		imagesavealpha($newimg, 1);
		
		imagealphablending($new_tr, true);
		
		$posx = 0;
		$posy = 0;
		
		// set x position
		if ($this->position == WatermarkTransformFactory::TOP_RIGHT ||
			$this->position == WatermarkTransformFactory::CENTER_RIGHT ||
			$this->position == WatermarkTransformFactory::BOTTOM_RIGHT)
		{
			$posx = $this->width - $t_width;
		}
		if ($this->position == WatermarkTransformFactory::TOP_CENTER ||
			$this->position == WatermarkTransformFactory::CENTER_CENTER ||
			$this->position == WatermarkTransformFactory::BOTTOM_CENTER)
		{
			$posx = $this->width/2 - $t_width/2;	
		}
		
		// set y position
		if ($this->position == WatermarkTransformFactory::TOP_CENTER ||
			$this->position == WatermarkTransformFactory::CENTER_CENTER ||
			$this->position == WatermarkTransformFactory::BOTTOM_CENTER)
		{
			$posy = $this->height/2 - $t_height/2;
		}
		if ($this->position == WatermarkTransformFactory::TOP_RIGHT ||
			$this->position == WatermarkTransformFactory::CENTER_RIGHT ||
			$this->position == WatermarkTransformFactory::BOTTOM_RIGHT)
		{
			$posy = $this->height - $t_height;
		}
		
		if ($this->alpha == 100)
		{
			imagecopy($newimg, $new_tr, $posx, $posy, 0, 0, $t_width, $t_height);
		}
		else
		{
			if ($this->transparent_image->getExtension() == 'png')
			{
				imagecopy($newimg, $new_tr, $posx, $posy, 0, 0, $t_width, $t_height);
				imagealphablending($newimg, false);
				imagesavealpha($newimg, true);
			}
			else if ($this->transparent_image->getExtension() == 'gif')
			{
				imagecopymerge($newimg, $new_tr, $posx, $posy, 0, 0, $this->width, $this->height, $this->alpha);
			}
			else
			{
				throw new Exception('Transprent not supported for file '.$this->transparent_image->getExtension());
			}
		}
		
		return $new_tr;
	}
}

?>