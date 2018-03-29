<?

	/*$width = 1100; 
	$height = 1100; 
	$bottom_image = imagecreatefrompng("img/heathertee.png"); 
	$top_image = imagecreatefrompng("img/shirt_shading.png"); 
	imagesavealpha($top_image, true); 
	imagealphablending($top_image, true); 
	
	imagesavealpha($bottom_image, true); 
	imagealphablending($bottom_image, true); 
	imagecopy($bottom_image, $top_image, 1100, 1100, 0, 0, $width, $height); 
	header('Content-type: image/png');
	imagepng($bottom_image);*/
	
	
	
	/*$x = 1100;
	$y = 1100;
	
	$final_img = imagecreatetruecolor($x, $y);
	
	
	imagesavealpha($final_img, true);
	
	
	$trans_colour = imagecolorallocatealpha($final_img, 0, 0, 0, 127);
	imagefill($final_img, 0, 0, $trans_colour);
	
	
	$images = array('img/heathertee.png', 'img/shirt_shading.png');
	
	foreach ($images as $image) {
	    $image_layer = imagecreatefrompng($image);
	    imagecopy($final_img, $image_layer, 0, 0, 0, 0, $x, $y);
	}
	
	//imagealphablending($final_img, true);
	imagesavealpha($final_img, true);
	imagealphablending($final_img, true);
	
	
	header('Content-Type: image/png');
	imagepng($final_img);*/
	
	
	
	
	
	$shirt = new \Imagick("./img/heathertee.png");
	$logo = new \Imagick("./img/shirt_art.png");
	$p1 = new \Imagick("./img/1_colorburn.png");
	$p2 = new \Imagick("./img/2_multiply.png");
	$p3 = new \Imagick("./img/3_screen.png");
	
	$shirt->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
	//$src1->setImageArtifact('compose:args', "1,0,-0.5,0.5");
	$shirt->compositeImage($logo, Imagick::COMPOSITE_DEFAULT, 0, 0);
	$shirt->compositeImage($p1, Imagick::COMPOSITE_COLORBURN, 0, 0);
	$shirt->compositeImage($p2, Imagick::COMPOSITE_MULTIPLY, 0, 0);
	$shirt->compositeImage($p3, Imagick::COMPOSITE_SCREEN, 0, 0);
	//$src1->writeImage("./output.png");
	// output the image to the browser as a png
	header( "Content-Type: image/png" );
	echo $shirt;
	

?>
