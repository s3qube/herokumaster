<?php
class testMFThumbs extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->load->model('products_model');
    	$this->load->model('users_model');
    	
    	//if($_SERVER['SCRIPT_FILENAME'] != 'purgeFailedUploads.php')
 		//	exit; 
 		
 		
 		// unzip all files in product!
 	
		/*$res = $zip->open('file.zip');
		if ($res === TRUE) {
		  $zip->extractTo('/myzips/extract_path/');
		  $zip->close();
		  echo 'woot!';
		} else {
		  echo 'doh!';
		}*/
 		

    }
    
   

}