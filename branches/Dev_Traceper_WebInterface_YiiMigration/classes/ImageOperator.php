<?php


class ImageOperator {
	private $imageDirectory;
	const thumbSuffix = '_thumb';
	private $missingImage;


  function __construct($imageDirectory, $missingImage) {
       $this->imageDirectory = $imageDirectory;
       $this->missingImage = $missingImage;
   }
	
	public function getImage($imageId, $thumb){
		if ($thumb == true)
		{
			$image = $this->imageDirectory . '/' . $imageId . self::thumbSuffix . '.jpg';
			if (file_exists($image) === false){
				$this->createThumb($imageId);
			}			
		}
		else {
			$image = $this->imageDirectory . '/' . $imageId . '.jpg';			
		}
		if (file_exists($image) === false){
			$image = $this->missingImage;
		}
		header('Content-Type: image/jpeg;');
	//	header('Content-Length: ' .filesize($image));
		return file_get_contents($image);		
	}
	
	private function createThumb($imageId) {	
	
		// Set maximum height and width
		$width  = 36;
		$height = 36;
		$filename =  $this->imageDirectory . '/' . $imageId . '.jpg';
		if (file_exists($filename) === true) {
			// Get new dimensions
			list($width_orig, $height_orig) = getimagesize($filename);

			$width = ($height / $height_orig) * $width_orig;


			// Resample
			$image_p = imagecreatetruecolor($width, $height);
			$image   = imagecreatefromjpeg($filename);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
			$filenameThumb =  $this->imageDirectory . '/' . $imageId . self::thumbSuffix .'.jpg';
			touch($filenameThumb);
			// Output
			imagejpeg($image_p, $filenameThumb);
			imagedestroy($image);
		}

	}
	
	public function deleteImage($imageId){
		$imageId = (int) $imageId;
		$orimg_path = $this->imageDirectory . '/' . $imageId . '.jpg';
		$thumbimg_path = $this->imageDirectory . '/' . $imageId . self::thumbSuffix . '.jpg';
		
		$out = SUCCESS;
		if(file_exists($orimg_path) == true)
		{
			if (unlink($orimg_path) != true){
				$out = FAILED;
			}
		}
		
		if(file_exists($thumbimg_path) == true)
		{
			if (unlink($thumbimg_path) != true){
				$out = FAILED;
			}
		}
		
		return $out;		
	}

}