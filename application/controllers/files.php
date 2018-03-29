<?php
class Files extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	
    	$this->load->model('files_model');
  
    }
    
    function upload($opm_productid,$fileType,$random_str = '')
	{
	
		error_log("Beginning UPLOAD!!!");
		error_log("FileType:".$fileType);
		error_log("Opm_productid:".$opm_productid);
		
		$this->opm->checkLogin();
		
		error_log("Passed Check LOGIN");

		@set_time_limit(0); // no time limit on uploads!
		
		$filesdata = print_r($_FILES, true);
		
		error_log("_FILES:".$filesdata);
		
		if ($_FILES['file']['name']) {
		
			error_log("we are beginning upload of " . $_FILES['file']['name']);
		
			$file = $_FILES['file']['tmp_name'];
			$error = false;
			
			// set up "arrData" for emails
			
			$arrData['filename'] = $_FILES['file']['name'];
			$arrData['username'] =  $this->userinfo->username;
	
			if (!is_uploaded_file($file)) {
				$error = '400 Bad Request';
				//header('HTTP/1.0 ' . $error);
				error_log('Error:' . "Upload appears to be invalid");
				die('Error:' . "Upload appears to be invalid");
	
			} else {
				 
				 if ($fileType == 'masterfile') {
				 
				 
				 	$fileid = $this->files_model->saveMasterFile($_FILES['file'],$opm_productid);
				 	$destDir = $this->config->item('fileUploadPath') . "masterfiles/" . $fileid;
				 	
				 	$this->opm->addHistoryItem($opm_productid,$_FILES['file']['name']." uploaded to masterfiles by " . $this->userinfo->username); 
					
					if (!$this->config->item("testServer"))
						$this->opm->sendProductEmail($opm_productid,"masterfile_uploaded",$arrData);
					
									 
				 } else if ($fileType == 'separation') {
				 	
				 	$fileid = $this->files_model->saveSeparation($_FILES['file'],$opm_productid);
				 	$destDir = $this->config->item('fileUploadPath') . "separations/" . $fileid;
				 
				 	$this->opm->addHistoryItem($opm_productid,$_FILES['file']['name']." uploaded to separations by " . $this->userinfo->username); 

				 	$this->opm->sendProductEmail($opm_productid,"separation_uploaded",$arrData);
				 
				 } else if ($fileType == 'asset') { // for assets, a bit different
				 	
				 	if ($fileid = $this->files_model->saveAsset($_FILES['file'],$opm_productid,$random_str)) { // in this case, opm_productid is the propertyid
				 		
				 		
				 		// see if we have a folder for propid, if not make it
				 		
				 		error_log("checking to see if dir exists...");
				 		
				 		if (!is_dir($this->config->item('fileUploadPath') . "assets/" . $opm_productid)) {
					 		
					 		error_log("it doesnt. makin dir...");
					 		
					 		if (mkdir($this->config->item('fileUploadPath') . "assets/" . $opm_productid)) {
						 		
						 		error_log("dir made!");
						 		
					 		} else {
						 		
						 		error_log("couldn't make dir");
						 		
					 		}
					 		
					 	}
				 		
				 		$destDir = $this->config->item('fileUploadPath') . "assets/" . $opm_productid . "/" . $random_str;
				 		
				 		//$this->opm->sendProductEmail($opm_productid,"asset_uploaded",$arrData);
				 
				 	} else {
				 	
				 		//header('HTTP/1.0 ' . " there is already a file with that name");
						die('Error:' . "There is already a file with that name.");
				 	
				 	}
				 	
				 	
				 }
				 
				error_log("DestDir:".$destDir); 
				 
				if (move_uploaded_file($file, $destDir)) {
				
					// now make a last check. attempt to read the file - if everything is okay... write confirmed flag to db entry.
					
					$origFileSize = $_FILES['file']['size'];
					
					if ($fileType == 'masterfile') {
					
						if ($diskFileSize = filesize($this->config->item('fileUploadPath') . "masterfiles/" . $fileid)) {
						
							if ($origFileSize == $diskFileSize)
								$this->files_model->confirmFileUpload($fileType,$fileid);
							else
								error_log("file '".$_FILES['file']['name']."' failed final check");
						
						} else {
						
							// if filesize fails, lets just approve the upload. 
							$this->files_model->confirmFileUpload($fileType,$fileid);
						
						}
						
					} else if ($fileType == 'separation') {
					
						if ($diskFileSize = @filesize($this->config->item('fileUploadPath') . "separations/" . $fileid)) {
							
							if ($origFileSize == $diskFileSize)
								$this->files_model->confirmFileUpload($fileType,$fileid);
							else
								error_log("file '".$_FILES['file']['name']."' failed final check");
							
						} else {
						
							// if filesize fails, lets just approve the upload.
							$this->files_model->confirmFileUpload($fileType,$fileid);
						
						}
					
					} else if ($fileType == 'asset') {
					
						$diskFileSize = @filesize($this->config->item('fileUploadPath') . "assets/" . $random_str);
					
						// let's make a thumbnail now.
						
						$im = new Imagick($destDir);
						$im->setImageBackgroundColor('white');

						$im = $im->flattenImages(); // Use this instead.
						
						$im->setImageFormat('jpg');
						
						$tnFileName = "TN_" . $random_str . ".jpg";
						
						if ($im->writeImage($this->config->item('fileUploadPath') . "assets/" . $opm_productid . "/" . $tnFileName)) {
							
							// thumbnail create successful. record it in db.
							
							$this->files_model->saveAssetThumbnail($fileid,$tnFileName);
							
							
						}
					
					} else {
					
					}
					
					
				
				
				 	error_log("file successfully uploaded");
					die('Upload Successfull');
				
				} else {
				
					error_log($_FILES['file']['name'] . " failed to complete!");
					die('Error:' . "Upload failed to complete!");
					
				
				}
	
			}

		}
	
	}
	
	function uploadAssets() {
	
		error_log("Beginning ASSET UPLOAD!!!");
		error_log("propertyid:".$_POST['propertyid']);
		
		$propertyid = $this->input->post('propertyid');
		
		$this->opm->checkLogin();
		
		error_log("Passed Check LOGIN");

		@set_time_limit(0); // no time limit on uploads!
		
		$filesdata = print_r($_FILES, true);
		
		error_log("_FILES:".$filesdata);
		
		//die("sall for now");
		
		if ($_FILES['file']['name']) { // valid upload
		
			error_log("we are beginning upload of " . $_FILES['file']['name']);
		
			$file = $_FILES['file']['tmp_name'];
			$error = false;
			
			// set up "arrData" for emails
			
			$arrData['filename'] = $_FILES['file']['name'];
			$arrData['username'] =  $this->userinfo->username;
			$arrData['userid'] =  $this->userinfo->userid;
	
			if (!is_uploaded_file($file)) {
				$error = '400 Bad Request';
				//header('HTTP/1.0 ' . $error);
				error_log('Error:' . "Upload appears to be invalid");
				die('Error:' . "Upload appears to be invalid");
	
			} else {
				 
			 	if ($fileid = $this->files_model->saveAsset($_FILES['file'],$propertyid)) { // in this case, opm_productid is the propertyid
			 		
			 		
			 		// see if we have a folder for propid, if not make it
			 		
			 		error_log("checking to see if dir exists...");
			 		
			 		if (!is_dir($this->config->item('fileUploadPath') . "assets/" . $propertyid)) {
				 		
				 		error_log("it doesnt. makin dir...");
				 		
				 		if (mkdir($this->config->item('fileUploadPath') . "assets/" . $propertyid)) {
					 		
					 		error_log("dir made!");
					 		
				 		} else {
					 		
					 		error_log("couldn't make dir");
					 		
				 		}
				 		
				 	}
			 		
			 		$destDir = $this->config->item('fileUploadPath') . "assets/" . $propertyid . "/" . $fileid;
			 		
			 		//$this->opm->sendProductEmail($opm_productid,"asset_uploaded",$arrData);
			 
			 	} else {
			 	
			 		//header('HTTP/1.0 ' . " there is already a file with that name");
					die('Error:' . "There is already a file with that name.");
			 	
			 	}
				 	
				 	
				error_log("DestDir:".$destDir); 
				 
				if (move_uploaded_file($file, $destDir)) {
				
					// now make a last check. attempt to read the file - if everything is okay... write confirmed flag to db entry.
					
					$origFileSize = $_FILES['file']['size'];

					$diskFileSize = @filesize($destDir);
				
					// let's make a thumbnail now.
					
					// first make thumbnails dir in prop if it don't exist
					
					error_log("checking to see if dir exists...");
			 		
			 		if (!is_dir($this->config->item('fileUploadPath') . "assets/" . $propertyid . "/thumbnails")) {
				 		
				 		error_log("it doesnt. makin dir...");
				 		
				 		if (mkdir($this->config->item('fileUploadPath') . "assets/" . $propertyid . "/thumbnails")) {
					 		
					 		error_log("dir made!");
					 		
				 		} else {
					 		
					 		error_log("couldn't make dir");
					 		
				 		}
				 		
				 	}
					
					$im = new Imagick($destDir);
					$im->setImageBackgroundColor('white');

					$im = $im->flattenImages(); // Use this instead.
					
					$im->setImageFormat('jpg');
					
					$tnFileName = $fileid . ".jpg";
					
					if ($im->writeImage($this->config->item('fileUploadPath') . "assets/" . $propertyid . "/thumbnails/" . $tnFileName)) {
						
						// thumbnail create successful. record it in db.
						
						$this->files_model->saveAssetThumbnail($fileid,$tnFileName);
						
						
					}
					
					
					
					
				
				
				 	error_log("file successfully uploaded");
					die('Upload Successfull');
				
				} else {
				
					error_log($_FILES['file']['name'] . " failed to complete!");
					die('Error:' . "Upload failed to complete!");
					
				
				}
	
			}

		}
	
	}
	
	
	/*function assetUpload($propertyid,$fileType,$random_str = '') {
	
		error_log("Beginning Asset Upload");
		error_log("FileType:".$fileType);
		error_log("Propertyid:".$propertyid);
		error_log("RandomStr:".$random_str);
		
		$random_str = random_str(20);
		
		$this->opm->checkLogin();
		
		error_log("Passed Check LOGIN");

		@set_time_limit(0); // no time limit on uploads!
		
		$filesdata = print_r($_FILES, true);
		
		error_log("_FILES:".$filesdata);
		
		if ($_FILES['file']['name']) {
		
			error_log("we are beginning upload of " . $_FILES['file']['name']);
		
			$file = $_FILES['file']['tmp_name'];
			$error = false;
			
			// set up "arrData" for emails
			
			$arrData['filename'] = $_FILES['file']['name'];
			$arrData['username'] =  $this->userinfo->username;
	
			if (!is_uploaded_file($file)) {
			
				$error = '400 Bad Request';
				//header('HTTP/1.0 ' . $error);
				error_log('Error:' . "Upload appears to be invalid");
				die('Error:' . "Upload appears to be invalid");
	
			} else {
				 
				 	
			 	if ($fileid = $this->files_model->saveAsset($_FILES['file'],$propertyid,$random_str)) { // in this case, opm_productid is the propertyid
			 		
			 		
			 		// see if we have a folder for propid, if not make it
			 		
			 		error_log("checking to see if dir exists...");
			 		
			 		if (!is_dir($this->config->item('fileUploadPath') . "assets/" . $propertyid)) {
				 		
				 		error_log("it doesnt. makin dir...");
				 		
				 		if (mkdir($this->config->item('fileUploadPath') . "assets/" . $propertyid)) {
					 		
					 		error_log("dir made!");
					 		
				 		} else {
					 		
					 		error_log("couldn't make dir");
					 		
				 		}
				 		
				 	}
			 		
			 		$destDir = $this->config->item('fileUploadPath') . "assets/" . $propertyid . "/" . $random_str;
			 		
			 		//$this->opm->sendProductEmail($opm_productid,"asset_uploaded",$arrData);
			 
			 	} else {
			 	
			 		//header('HTTP/1.0 ' . " there is already a file with that name");
					die('Error:' . "There is already a file with that name.");
			 	
			 	}
				 					 
				error_log("DestDir:".$destDir); 
				 
				if (move_uploaded_file($file, $destDir)) {
				
					// now make a last check. attempt to read the file - if everything is okay... write confirmed flag to db entry.
					
					$origFileSize = $_FILES['file']['size'];

					$diskFileSize = @filesize($this->config->item('fileUploadPath') . "assets/" . $random_str);
				
					// let's make a thumbnail now.
					
					$im = new Imagick($destDir);
					$im->setImageBackgroundColor('white');
	
					$im = $im->flattenImages(); // Use this instead.
					
					$im->setImageFormat('jpg');
					
					$tnFileName = "TN_" . $random_str . ".jpg";
					
					if ($im->writeImage($this->config->item('fileUploadPath') . "assets/" . $propertyid . "/" . $tnFileName)) {
						
						// thumbnail create successful. record it in db.
						
						$this->files_model->saveAssetThumbnail($fileid,$tnFileName);
						
					}
					
				 	error_log("file successfully uploaded");
					die('Upload Successfull');
				
				} else {
				
					error_log($_FILES['file']['name'] . " failed to complete!");
					die('Error:' . "Upload failed to complete!");
					
				
				}
	
			}

		}
	
	}*/
	
	function guestUpload($random_str)
	{
		// get info in guest upload!
		
		error_log('Error:' . "beginning uploady");
		
		$this->load->model('guestdownload_model');

		if (!$gd = $this->guestdownload_model->fetchDownload($random_str)) {
		
			error_log('Error:' . "Couldn't find guest downcccload info");
			die('Error:' . "Couldn't find guest download info");
		
		}
		
		$opm_productid = $gd->opm_productid;
    		

		@set_time_limit(0); // no time limit on uploads!
		
		
		
		if ($_FILES['Filedata']['name']) {
		
			error_log("we are beginning guest upload of " . $_FILES['Filedata']['name']);
		
			$file = $_FILES['Filedata']['tmp_name'];
			$error = false;
			
			// set up "arrData" for emails
			
			$arrData['filename'] = $_FILES['Filedata']['name'];
			$arrData['username'] =  $gd->username;
	
			if (!is_uploaded_file($file)) {
				$error = '400 Bad Request';
				//header('HTTP/1.0 ' . $error);
				error_log('Error:' . "Upload appears to be invalid");
				die('Error:' . "Upload appears to be invalid");
	
			} else {
				 
				 if ($gd->filetype == 'mf') {
				 
				 
				 	$fileid = $this->files_model->saveMasterFile($_FILES['Filedata'],$opm_productid);
				 	$destDir = $this->config->item('fileUploadPath') . "masterfiles/" . $fileid;
				 	
				 	$this->opm->addHistoryItem($opm_productid,$_FILES['Filedata']['name']." was uploaded to masterfiles by " . $arrData['username'] . " (Guest Uploader)"); 
					
					if (!$this->config->item("testServer"))
						$this->opm->sendProductEmail($opm_productid,"masterfile_uploaded",$arrData);
					
									 
				 } else if ($gd->filetype == 'sep') {
				 	
				 	$fileid = $this->files_model->saveSeparation($_FILES['Filedata'],$opm_productid);
				 	$destDir = $this->config->item('fileUploadPath') . "separations/" . $fileid;
				 
				 	$this->opm->addHistoryItem($opm_productid,$_FILES['Filedata']['name']." was uploaded to separations by " . $arrData['username'] . " (Guest Uploader)"); 

				 	$this->opm->sendProductEmail($opm_productid,"separation_uploaded",$arrData);
				 
				 
				 } else {
				 
				 	error_log('Error:' . "Invalid File Type");
					die('Error:' . "Invalid File Type");
				 
				 }
				 	
				if (move_uploaded_file($file, $destDir)) {
				
					// now make a last check. attempt to read the file - if everything is okay... write confirmed flag to db entry.
					
					$origFileSize = $_FILES['Filedata']['size'];
					
					if ($gd->filetype == 'mf') {
						
						$fileType = 'masterfile';
						$diskFileSize = @filesize($this->config->item('fileUploadPath') . "masterfiles/" . $fileid);
						
						if ($origFileSize == $diskFileSize)
							$this->files_model->confirmFileUpload($fileType,$fileid);
						else
							error_log("file '".$_FILES['Filedata']['name']."' failed final check");
						
					} else if ($gd->filetype == 'sep') {
					
						$fileType = 'separation';
						$diskFileSize = @filesize($this->config->item('fileUploadPath') . "separations/" . $fileid);
						
						if ($origFileSize == $diskFileSize)
							$this->files_model->confirmFileUpload($fileType,$fileid);
						else
							error_log("file '".$_FILES['Filedata']['name']."' failed final check");
						
					} else {
					
					}
					
					$this->guestdownload_model->disableDownload($gd->id);
				 	error_log("file successfully uploaded");
					die('Upload Successfull');
					
				
				} else {
				
					die('Error:' . "Upload failed to complete!");
					error_log($_FILES['Filedata']['name'] . "failed to complete!");
				
				}
	
			}
			
			
			
			
			
			
	
		}
	
	}	

	
	function download($fileType,$id) {
		
		$this->opm->checkLogin();
		$this->load->helper('file');
	
		if ($fileType == 'mf' || $fileType == 'sep' || $fileType == 'asset') {
			
			if ($fileType == 'mf') {
			
				if (checkPerms('can_view_masterfiles',true)) { 
				
					if(!$fileinfo = $this->files_model->fetchMasterFile($id))
						$this->opm->displayError("file_not_found");
						
					$filepath = $this->config->item('fileUploadPath') . "masterfiles/" . $fileinfo->fileid;
				
				}
				
			} else if ($fileType == 'sep') {
			
				if (checkPerms('can_view_separations',true)) { 
			
					if (!$fileinfo = $this->files_model->fetchSeparation($id))
						$this->opm->displayError("file_not_found");
					
					$filepath = $this->config->item('fileUploadPath') . "separations/" . $fileinfo->fileid;
				
				}
			
			} else if ($fileType == 'asset') {
				
				//die("bingo boys");
				
				if (checkPerms('can_view_assets',true)) { 
				
					if (!$fileinfo = $this->files_model->fetchAsset($id))
						$this->opm->displayError("file_not_found");
					
					$filepath = $this->config->item('fileUploadPath') . "assets/" . $fileinfo->propertyid . "/" . $fileinfo->assetid;
				
					error_log("downloading asset: " . $filepath);
				
				}
			}
			
			//die($fileinfo->filename);
			
			
			
			if (file_exists($filepath)) {
			
				if (isset($fileinfo->opm_productid)) // add history if DL is for product
					$this->opm->addHistoryItem($fileinfo->opm_productid,$this->userinfo->username." downloaded " . $fileinfo->filename); 
				
				//header("Content-type: application/pdf");
				header("Content-type: application/force-download"); 
				header('Content-Type: application/octet-stream'); 
				
				header("Content-Disposition: attachment; filename=\"" . $fileinfo->filename . "\"");
				header("Pragma: no-cache");
				header("Expires: 0");
			
				//ob_end_flush();
				
				$filedata = read_file($filepath);

				echo $filedata;
				exit();
			
			
			} else {
			
				$this->opm->displayError("couldn't read file");
			
			}
				
		
		} else {
		
			$this->opm->displayError("invalid_file_type");
		}
		
	}
	
	function delete($fileType,$id) {
		
		$this->opm->checkLogin();
		$this->load->helper('file');
		
		
	
		if ($fileType == 'mf' || $fileType == 'sep' || $fileType == 'asset') {
			
			if ($fileType == 'mf') {
			
				if (checkPerms('can_delete_masterfiles',true)) { 
				
					if(!$fileinfo = $this->files_model->fetchMasterFile($id))
						$this->opm->displayError("file_not_found");
						
						//Globosoft
						$this->opm->addHistoryItem($fileinfo->opm_productid,$this->userinfo->username." Deleted " . $fileinfo->filename); 
						//
					$filepath = $this->config->item('fileUploadPath') . "masterfiles/" . $fileinfo->fileid;
					
					
					if (is_file($filepath))
						unlink($filepath);
					
					$this->files_model->deleteMasterFile($id);
					
				
					$this->opm->displayAlert("File successfully deleted!","/products/view/" . $fileinfo->opm_productid . "/images");
					return true;
					
				}
				
				
			} else if ($fileType == 'sep') {
				
				if (checkPerms('can_delete_separations',true)) { 
				
					
					if(!$fileinfo = $this->files_model->fetchSeparation($id))
						$this->opm->displayError("file_not_found");
						//Globosoft
						$this->opm->addHistoryItem($fileinfo->opm_productid,$this->userinfo->username." Deleted " . $fileinfo->filename);  
						//
					$filepath = $this->config->item('fileUploadPath') . "separations/" . $fileinfo->fileid;
					
					if (is_file($filepath))
						unlink($filepath);
					
					$this->files_model->deleteSeparation($id);
					
					$this->opm->displayAlert("File successfully deleted!","/products/view/" . $fileinfo->opm_productid . "/images");
					return true;
			
				
				}
			
			
			} else if ($fileType == 'asset') {
				
				
				if (checkPerms('can_delete_assets',true)) { 
				
					
					if(!$fileinfo = $this->files_model->fetchAsset($id))
						$this->opm->displayError("file_not_found");
						//Globosoft
					$this->opm->addHistoryItem($fileinfo->opm_productid,$this->userinfo->username." Deleted " . $fileinfo->filename); 
					//
					$filepath = $this->config->item('fileUploadPath') . "assets/" . $fileinfo->serverfilename;
					
					if (is_file($filepath))
						unlink($filepath);
					
					$this->files_model->deleteAsset($id);
					
					$this->opm->displayAlert("File successfully deleted!","/properties/view/" . $fileinfo->propertyid . "/assets");
					return true;
				
				}
				
			
			}
				
		
		} else {
		
			$this->opm->displayError("invalid_file_type");
		}
		
	}
	
	function preparePropertyArchive($propertyID) {
		
		$this->opm->checkLogin();
		$this->opm->opmInit();
		
		$this->load->model('properties_model');
		$this->load->model('products_model');
		
		if (!$p = $this->properties_model->fetchPropertyInfo($propertyID)) {
		
			$this->opm->displayError("Property Not Found");
			return false;
		
		}
		
		// create archive name string
		
		$archiveName = str_replace(" ", "_", $p->property) . "_Archive_" . mktime();
		
		$tmpDirName = $archiveName;
		
		// make new dir
		
		mkdir($this->config->item('fileUploadPath')."temp/" . $tmpDirName, 0700);
		mkdir($this->config->item('fileUploadPath')."temp/" . $tmpDirName . "/masterfiles", 0700);
		mkdir($this->config->item('fileUploadPath')."temp/" . $tmpDirName . "/separations", 0700);
	
		// ASSEMBLE MASTER FILES
		
		if (!$files = $this->files_model->fetchMasterFilesByPropertyID($propertyID))
			$this->opm->displayError("Property Not Found");
		
		foreach ($files->result() as $f) {

			$origFilePath = $this->config->item('fileUploadPath') . "masterfiles/" . $f->fileid;
			$newFilePath = $this->config->item('fileUploadPath') . "temp/" . $tmpDirName . "/masterfiles/" . str_replace(" ", "_", $f->filename);
			
			if (!file_exists($newFilePath)) {
			
				$cmd = "cp " . $origFilePath . " " . $newFilePath;
				
				$output = shell_exec($cmd);
				echo "<pre>$cmd</pre>";
			
			}
		
		}
		
		// ASSEMBLE SEPARATIONS
		
		if (!$files = $this->files_model->fetchSeparationsByPropertyID($propertyID))
			$this->opm->displayError("Property Not Found");
		
		foreach ($files->result() as $f) {

			$origFilePath = $this->config->item('fileUploadPath') . "separations/" . $f->fileid;
			$newFilePath = $this->config->item('fileUploadPath') . "temp/" . $tmpDirName . "/separations/" . str_replace(" ", "_", $f->filename);
			
			if (!file_exists($newFilePath)) {
			
				$cmd = "cp " . $origFilePath . " " . $newFilePath;
				
				$output = shell_exec($cmd);
				echo "<pre>$cmd</pre>";
			
			}
		
		}
		
		// ZIP THE WHOLE THING AND PUT IT IN A WEB ACCESSIBLE AREA.
		
		$zipFilePath = $this->config->item('fileArchivePath') . $tmpDirName . ".zip";
		$tmpDirPath = $this->config->item('fileUploadPath') . "temp/" . $tmpDirName;
		
		$cmd = "zip -r " . $zipFilePath . " " . $tmpDirPath;
		
		$output = shell_exec($cmd);
		echo "<pre>$cmd</pre>";
	
	}

}
?>