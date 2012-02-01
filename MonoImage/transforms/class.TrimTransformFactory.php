<?php
require_once(dirname(__FILE__).'/class.TransformFactory.php');
class TrimTransformFactory extends TransformFactory 
{
	/**
	 * Constructor.
	 *
	 * @param int $width			new image width
	 * @param int $height			new image height
	 * @return TrimTransformFactory	
	 */
	public function TrimTransformFactory($width, $height)
	{
		$this->setWidth($width);
		$this->setHeight($height);
	}
	
	/**
	 * Transfrom & trim input resource to new resource with new width and height.
	 *
	 * @param resource $oldimage	input image gd resource
	 * @return resource				new image resource 
	 */
	public function transformImage($oldimage)
	{
		$old_width	= imagesx($oldimage);
		$old_height	= imagesy($oldimage);
		
		list($width, $height, $old_width, $old_height, $xpos, $ypos) = $this->countSize($old_width, $old_height, $this->width, $this->height);
		
		$newimg = $this->createResource($width, $height);
		imagecopyresampled(
			$newimg, 
			$oldimage, 
			0, 
			0, 
			$xpos, 
			$ypos, 
			$width, 
			$height, 
			$old_width, 
			$old_height
		);
		
		imagedestroy($oldimage);
		return $newimg;
	}

	/**
	 * zachovava pomer stran a potom az oreze, resizne
	 */
	public static function countSize($old_width, $old_height, $max_width, $max_height)
	{
		#resizne a trimne obrazok - povodny obrazok presahuje rozmery, rovnomerne zmensime a orezeme
		if($old_width >= $max_width && $old_height >= $max_height)
		{
			$width_rate		= $old_width / $max_width;
			$height_rate	= $old_height / $max_height;
			if ($width_rate > $height_rate)
			{
				$width		= round($max_width * $height_rate);
				$height		= round($max_height * $height_rate);
				$complement	= floor(($old_width - $width) / 2);
				$x			= $complement;
				$y			= 0;
			}
			else
			{
				$width		= round($max_width * $width_rate);
				$height		= round($max_height * $width_rate);
				$complement	= floor(($old_height - $height) / 2);
				$x			= 0;
				$y			= $complement;
			}
			return array($max_width, $max_height, $width, $height, $x, $y);
		}
		#presahuje len vysku - resizne obrazok podla vysky
		else if($old_height >= $max_height)
		{
			$height_rate	= $max_height / $old_height;
			$width		= round($old_width * $height_rate);
			$height		= round($old_height * $height_rate);
			return array($width, $height, $old_width, $old_height, 0, 0);
		}
		#presahuje len sirku - resizne obrazok podla sirky
		else if($old_width >= $max_width)
		{
			$width_rate	= $max_width / $old_width;
			$width		= round($old_width * $width_rate);
			$height		= round($old_height * $width_rate);
			return array($width, $height, $old_width, $old_height, 0, 0);
		}
		#nepresahuje - necha povodny
		else
		{
			return array($old_width, $old_height, $old_width, $old_height, 0, 0);
		}
	}
};
?>