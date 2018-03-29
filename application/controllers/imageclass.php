<?php
class Imageclass extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->load->model('products_model');
    	$this->load->model('images_model');
    	
    	if ($_SERVER['REMOTE_ADDR'] != $this->config->item('localIP')) // verify credentials if not coming from localhost (PDF GEN comes from localhost and can't authenticate)
    		$this->opm->checkLogin();
    }
    
    
    function imageViewer($imageid,$width) {
    
    	// figure out what widths are avail.
    	
    	$i = $this->products_model->fetchImage($imageid);
    	
		$filePath = $this->config->item('fileUploadPath') . "visuals/" . $imageid;
    	$imageDims = getimagesize($filePath);
    	
    	//$imageDims = getJPEGImageXY($i->image);
    	$data['imageActualSize'] = $imageDims[0];
    	
    	$data['imageid'] = $imageid;
    	$data['width'] = $width;
    	
    	$data['otherImages'] = $this->images_model->fetchImages($i->opm_productid);
    	
    	$this->load->view('imageViewer',$data);
    
    }
    
    function imageViewerDims($imageid,$width) {
    
    	echo "<pre>";
    
    	// figure out what widths are avail.
    	
    	$filePath = $this->config->item('fileUploadPath') . "visuals/" . $imageid;
    	$imageDims = getimagesize($filePath);
    	
    	//$i = $this->products_model->fetchImage($imageid);
    	//$imageDims = getJPEGImageXY($i->image);
    	    	
    	$data['imageActualSize'] = $imageDims[0];
    	$data['imageid'] = $imageid;
    	$data['width'] = $width;

    	$this->load->view('imageViewerDims',$data);
    
    }

	
	function view($id, $download = false, $width = null) {
    
    	if ($image = $this->products_model->fetchImage($id)) {
     
			if (!$download) {
			
				header("Content-type: " . $image->image_type);
			
			} else { // download mode!
			
				// determine filename
			
				if ($image->filename) {
				
					$filename = $image->filename;
				
				} else {
					
					$product = $this->products_model->fetchProductInfo($image->opm_productid,true); // litemode!
					$filename = $product->property ."_" . $product->productname . "_" . $image->imageid;
					$filename = str_replace(" ", "_", $filename);
					$filename = str_replace("/", "_", $filename);
				
				}
					
					
				// determine file extension 
				
				if ($image->image_type == 'image/pjpeg' || $image->image_type == 'image/jpeg')
					$fileExtension = '.jpg';
				else if ($image->image_type == 'image/gif')
					$fileExtension = '.gif';
				else if ($image->image_type == 'image/bmp')
					$fileExtension = '.bmp';
				else if ($image->image_type == 'image/png')
					$fileExtension = '.png';
				else
					$fileExtension = '';
			
				header("Pragma: public"); // required
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private",false); // required for certain browsers 
				header("Content-Type: " . $image->image_type);
				// change, added quotes to allow spaces in filenames, by Rajkumar Singh
				header("Content-Disposition: attachment; filename=\"".$filename."\";" );
				header("Content-Transfer-Encoding: binary");
				//header("Content-Length: ".$filename);
			
			
			}
		 
		 	$filePath = $this->config->item('fileUploadPath') . "visuals/" . $image->imageid;
    	
    		$fh = fopen($filePath, 'r');
			$imageData = fread($fh, filesize($filePath));
			fclose($fh);
			
			echo $imageData;
		
		
		} else {
		
			$this->opm->displayError("Image does not exist");
			return false;		
		}
      	
      	    
    }
    
    function viewAvatar($userid) {
   
	    $this->load->model('users_model');
		 
		if ( ($user = $this->users_model->fetchUserInfo($userid)) && ($user->avatar_path) ) {
    	
    		//die("hi");
    	
    		$filePath = $this->config->item('fileUploadPath') . "avatars/" . $user->avatar_path;
    	
			$fh = fopen($filePath, 'r');
			$imageData = fread($fh, filesize($filePath));
			fclose($fh);
			
			$size = getimagesize($filePath);
			header("Content-type: {$size['mime']}");
			
			echo $imageData;
    	
    	} else {
    	
    		die("hi");
    	
    	} 
		 	    
    }
    
    function viewInvoiceImage($invoiceid,$userid = 0) {
    
    	$this->load->model('invoices_model');
		$fullImagePath = "";
    
    	if ($i = $this->invoices_model->fetchInvoice($invoiceid)) {
    	
			$fullImagePath = $this->config->item('fileUploadPath') . "invoiceImages/" . $i->invoice_imagepath;
    	
    	} elseif ($userid && ($u = $this->users_model->fetchUserInfo($userid))) {
    	
    		$fullImagePath = $this->config->item('fileUploadPath') . "invoiceImages/" . $u->invoiceimage_path;

    	
    	}
    	
    	if ($fullImagePath) {
    	
			$fh = fopen($fullImagePath, 'r');
			$theData = fread($fh, filesize($fullImagePath));
			fclose($fh);
			
			// determine mime type
			
			$size = getimagesize($fullImagePath);
			
			header("Content-type: {$size['mime']}");
		 
			
			echo $theData;
		
		
		} else {
		
			$this->opm->displayError("Image does not exist");
			return false;		
		}
      	
      	    
    }
    
    function makeDefault($opm_productid,$imageid) {
    
    	if ($this->images_model->setDefaultImageID($opm_productid,$imageid))  {
    	
    		$this->opm->addHistoryItem($opm_productid,"Image ID # " . $imageid . " set to default by " . $this->userinfo->username); 
     		$this->opm->displayAlert("New Default Image Set!","/products/view/" . $opm_productid);
			return true;
    	
    	} else {
    	
    		$this->opm->displayError("Image does not exist");
			return false;	
    	
    	}
    	
    
    }
    
    function viewAssetThumbnail($assetid,$width = 150) {
    
    	$this->load->model('properties_model');
    	$this->load->model('assets_model');
    	
    	$image = $this->assets_model->fetchAssetInfo($assetid);
    	
    	$filePath = $this->config->item('fileUploadPath') . "assets/" . $image->propertyid . "/thumbnails/" . $assetid . ".jpg";//$image->propertyid . "/" . $image->serverthumbnailname;

    	if (@$fh = fopen($filePath, 'r')) { // we gotta thumbnail
	    	
	    	//print_r($image);
	    	//die();
	    	
	    	header("Content-type: image/jpeg");
	    	
	    	$size = $width;  // new image width
	    	
	    	// read image from file.
	    	
	    //	$filePath = $this->config->item('fileUploadPath') . "assets/" . $assetid;//$image->propertyid . "/" . $image->serverthumbnailname;

	    	//tmp path chg
	    	
    		
			$imageData = fread($fh, filesize($filePath));
			//die($imageData);
			fclose($fh);
    	
    		$src = imagecreatefromstring($imageData); 

			$width = imagesx($src);
			$height = imagesy($src);
			$aspect_ratio = $height/$width;
			
			if ($width <= $size) {
				
				$new_w = $width;
				$new_h = $height;
				
			} else {
				
				$new_w = $size;
				$new_h = abs($new_w * $aspect_ratio);
			
			}
			
			$img = imagecreatetruecolor($new_w,$new_h);
			
			imagealphablending($img, true); // setting alpha blending on
			imagesavealpha($img, true); 
			  
			imagecopyresampled($img,$src,0,0,0,0,$new_w,$new_h,$width,$height);
			
			imagealphablending($img, true); // setting alpha blending on
			imagesavealpha($img, true);
			
			// determine image type and send it to the client    
			imagejpeg($img); 

			//imagejpeg($img); 
			imagedestroy($img);
	    	
	    	
     
     		
		} else { // oops, don't got it.
		
    		$filename = base_url()."resources/images/no_image.gif";
    		$x = array_change_key_case(get_headers($filename, 1),CASE_LOWER);
    		$x = $x['content-length'];
			$fh = fopen($filename, 'r');
			$theData = fread($fh, $x);
			fclose($fh);
			
			header("Content-type: image/gif");
			echo $theData;
			return true;
		
		}

      	
      	    
    }
    
    function viewThumbnail($id, $thewidth = 60, $fileExtensionKludge=".jpg") {
    
    	if (!$image = $this->products_model->fetchImage($id)) {

    		// grab an empty gif?
    		$filename = base_url()."resources/images/no_image.gif";
    		$x = array_change_key_case(get_headers($filename, 1),CASE_LOWER);
    		$x = $x['content-length'];
			$fh = fopen($filename, 'r');
			$imageData = fread($fh, $x);
			fclose($fh);
			
			$src = imagecreatefromstring($imageData);
			
			$width = imagesx($src);
			$height = imagesy($src); 
			
			$img = imagecreatetruecolor($thewidth,$thewidth); 
		  	imagecopyresampled($img,$src,0,0,0,0,$thewidth,$thewidth,$width,$height);
			
			header("Content-type: image/gif");
			
			imagegif($img);
			imagedestroy($img);
			
			//echo $theData;
			return true;
    	
    	}
    	
    	
    	header("Content-type: " . $image->image_type);
    	
    	$size = $thewidth;  // new image width
    	
    	// read image from file.
    	
    	$filePath = $this->config->item('fileUploadPath') . "visuals/" . $image->imageid;
    	
    	
    	
    	
    	if ($image->image_type == 'image/png' || $image->image_type == 'image/x-png') {
 			
 		//	$src = imagecreatefrompng($filePath);
 			
 			
 			
			$src = imagecreatefrompng($filePath);
			$width = imagesx($src);
			$height = imagesy($src); 
    		
    		 $newImg = imagecreatetruecolor($size, $size);
			 imagealphablending($newImg, false);
			 imagesavealpha($newImg,true);
			 $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
			 imagefilledrectangle($newImg, 0, 0, $size, $size, $transparent);
			 imagecopyresampled($newImg, $src, 0, 0, 0, 0, $size, $size, $width, $height);
    		
    		imagepng($newImg);
    		imagedestroy($newImg);
    		die();
    	
    	} else {
    	
    		$fh = fopen($filePath, 'r');
			$imageData = fread($fh, filesize($filePath));
			fclose($fh);
    	
    		$src = imagecreatefromstring($imageData); 
    		
    	
    	}
    	
		
		
		
		$width = imagesx($src);
		$height = imagesy($src);
		$aspect_ratio = $height/$width;
		
		if ($width <= $size) {
			$new_w = $width;
			$new_h = $height;
		} else {
			$new_w = $size;
			$new_h = abs($new_w * $aspect_ratio);
		}
		
		$img = imagecreatetruecolor($new_w,$new_h);
		
		imagealphablending($img, true); // setting alpha blending on
		imagesavealpha($img, true); 
		  
		imagecopyresampled($img,$src,0,0,0,0,$new_w,$new_h,$width,$height);
		
		imagealphablending($img, true); // setting alpha blending on
		imagesavealpha($img, true);
		
		// determine image type and send it to the client    
		if ($image->image_type == "image/pjpeg" || $image->image_type == "image/jpeg") {    
			imagejpeg($img); 
		} else if ($image->image_type == "image/x-png" || $image->image_type == "image/png") {
			imagepng($img);
		} else if ($image->image_type == "image/gif") {
			imagegif($img);
		}
		   
		//imagejpeg($img); 
		imagedestroy($img);
	    
    }
    
    function delete($id, $download = false) {
    
    	
    	if ($product->islocked && !checkPerms('can_edit_locked_products')) {

			alert("Product is locked. Image cannot be deleted.");
			return false;

		} else {
		
		
	    	if ($opm_productid = $this->images_model->deleteImage($id)) {
	     	
	     		$this->opm->addHistoryItem($opm_productid,"Image deleted by " . $this->userinfo->username); 
	     		$this->opm->displayAlert("Image Has Been Deleted!","/products/view/" . $opm_productid);
				return true;
	     	
	     	} else {
	
	     		$this->opm->displayError("Image could not be deleted.");
	
	     	
	     	}
	     	
	     }
	}
    
}

?>