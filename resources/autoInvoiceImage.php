<?php

$text = stripslashes($_GET['text']);
$my_img = imagecreate( 500, 62 );

$background = imagecolorallocate( $my_img, 255, 255, 255 );
$text_colour = imagecolorallocate( $my_img, 255, 255, 0 );

//imagestring( $my_img, 4, 30, 25, "thesitewizard.com",$text_colour );

$black = imagecolorallocate($my_img, 0, 0, 0);
$font = "Georgia.ttf";

imagettftext($my_img, 28, 0, 0, 35, $black, $font, $text);

header( "Content-type: image/png" );
imagepng( $my_img );

imagecolordeallocate( $text_color );

imagedestroy( $my_img );
?>