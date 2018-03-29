<?php
class Properties extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('properties_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'properties';
    	
    	global $searchArgList;
    	$searchArgList = array("searchText","firstLetter","showDeactivated");
    	
    }

	function index() // index will be the property list.
	{
	
		redirect("/properties/search");	

	}
	
	function search($searchText = 0, $firstLetter = null, $showDeactivated = 0) // index will be the property list.
	{
		
		global $searchArgList;
		
		$searchText = urldecode($searchText);
		
		foreach ($searchArgList as $k=>$d) 
    		$data['args'][$d] = ${$d}; 
    		
    	//print_r($data['args']);
		
		$data['firstLetters'] = $this->properties_model->fetchFirstLetterLinks($firstLetter);
		$data['firstLetter'] = $firstLetter;
		
		if ($showDeactivated)
			$fetchD = true;
		else
			$fetchD = false;
	
		
		$template['nav2'] = "Search Properties";
		
		$template['page_title'] = "View Properties";
		$template['bigheader'] = "Search Properties";
		$template['searchArea'] = $this->load->view('properties/searchArea',$data,true);
		$template['rightNav'] = $this->load->view('properties/rightNav',$data,true);
		

		
		if ($firstLetter || $searchText) {
		
			$data['properties'] = $this->properties_model->fetchProperties($fetchD,true,false,$searchText,0,0,$firstLetter);
			$data['totalProperties'] = $this->properties_model->fetchProperties($fetchD,true,true,$searchText,0,0,$firstLetter);

			$template['content'] = $this->load->view('properties/search', $data,true);
		
		}
		
		$template['contentNav'] = $this->load->view('properties/searchNav',$data,true);

		
        $arrJS['scripts'] = array('mootools-release-1.11','opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
        
		$this->load->view('global/maintemplate', $template);
	
	}
	
	function submit() { // construct a search url from a form submission
    
    	global $searchArgList;

		foreach ($searchArgList as $key=>$data) {
			
			if ($this->input->post($data))
				$segments[$data] = $this->input->post($data);
			else
				$segments[$data] = 0;	
			
		}
		
		$url = "/properties/search/";
		
		foreach ($segments as $data)
			$url .= $data . "/";
			
		
		redirect($url);
			
	
    
    }
	
	
	
	function view($id,$tabname = 'basicinfo')
    {
    
     
     	if ($tabname)
    		$data['tabname'] = $tabname;
    	else
    		$data['tabname'] = 'basicinfo';
    		
    	$data['mode'] = 'edit';	
    		
    	$data['p'] = $this->properties_model->fetchPropertyInfo($id);
    	
    	$template['content'] = '';
     	$template['headInclude'] = $this->load->view('properties/loadContentJS',$data,true);;
     
        
        $template['nav2'] = "<a href=\"".base_url()."properties/search/\">Properties</a>&nbsp;&nbsp;>&nbsp;&nbsp;" . $data['p']->property;;
        $template['page_title'] = "View Property - " . $data['p']->property;
		$template['bigheader'] = $data['p']->property;
		$template['contentNav'] = $this->load->view('properties/contentNav',$data,true);

		$arrJS['scripts'] = array('jquery-1.6.2.min','opm_scripts','shadowbox3','datepicker'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		
		$this->load->view('global/maintemplate', $template);
   
    }
    
    function editProductLine($productLineID,$propertyID) {
	
		$data = array();

		$this->load->model('productlines_model');
		$data['productLine'] = $this->productlines_model->fetchProductLine($productLineID)->row();
		$data['propertyid'] = $propertyID;
		
		
    	$template['content'] = $this->load->view('properties/editProductLine',$data,true);     
        
        $template['nav2'] = "Edit Product Line";
        $template['page_title'] = "Edit Product Line";
		$template['bigheader'] = "Edit Product Line";

		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','datepicker'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		
		$this->load->view('global/maintemplate', $template);
	
	}
	
	function updateProductLine() {
	
	
		$this->load->model('productlines_model');
	
		if (checkPerms('can_add_product_lines',true)) {
		
			$errors = "";
		    $postdata['propertyid'] =$this->input->post('propertyid');
		    
			if (!$postdata['productlineid'] = $this->input->post('productlineid'))
				$errors .= "Unknown Error<br />";
				
			if (!$postdata['productline'] = $this->input->post('productline'))
				$errors .= "It appears that you did not enter a product line.<br />";
			
			if ($errors) {
		
				$this->opm->displayError($errors);
				return false;
			
			}
				
			if ($this->productlines_model->updateProductLine($postdata['productlineid'],$postdata['productline'])) {
				
				$this->opm->displayAlert("Product Lines Have Been Saved!","/properties/view/" . $postdata['propertyid']);
				return true;	
			
			} else {
			
				$this->opm->displayError("There was an error saving the Product Lines");
				return true;
			
			}

		}
	
	}
	
	
	function deleteProductLine($productlineid,$propertyid) {
		
		$this->load->model('productlines_model');
		
		if (checkPerms('can_delete_productlines',true)) {
		
			// first check if products exist.
			
			if ($this->productlines_model->checkForProducts($productlineid)) {
			
				$this->opm->displayError("Product Line cannot be deleted because products are assigned to it.");
				return false;
			
			}
	
			if ($this->productlines_model->deleteProductLine($productlineid)) {
				
				$this->opm->displayAlert("Product Line Has Been Deleted!","/properties/view/" . $propertyid);
				return true;	
			
			} else {
			
				$this->opm->displayError("There was an error delete the Product Lines");
				return true;
			
			}

		}
		
	}
	
    
    function add()
    {
		
		$data = array();
		
		$data['mode'] = "add";
		
		$this->load->model('approvalmethods_model');
    	$data['appMethods'] = $this->approvalmethods_model->fetchApprovalMethods();
		
    	$template['content'] = $this->load->view('properties/basicinfo',$data,true);
     	//$template['headInclude'] = $this->load->view('properties/loadContentJS',$data,true);
     
        
        $template['nav2'] = "Add New Property";
        $template['page_title'] = "Add New Property";
		$template['bigheader'] = "Add New Property";
		//$template['contentNav'] = $this->load->view('properties/contentNav',$data,true);

		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','datepicker'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		
		$this->load->view('global/maintemplate', $template);
    }
    
    function showAllFiles($propertyid)
    {
    
    	if (checkPerms('can_view_all_files_list',true)) {
    	
    		$data['files'] = array();
    	
	    	$this->load->model('files_model');
	    	
	    	$data['mfs'] = $this->files_model->fetchMasterFilesByPropertyID($propertyid);
			$data['seps'] = $this->files_model->fetchSeparationsByPropertyID($propertyid);
			
			$data['mfs'] = $this->files_model->fetchMasterFilesByPropertyID($propertyid);
			$data['seps'] = $this->files_model->fetchSeparationsByPropertyID($propertyid);

			foreach ($data['mfs']->result() as $f)
				$data['files'][] = array("dbtype"=>"Masterfile","fileid"=>$f->fileid,"filename"=>$f->filename,"productname"=>$f->productname,"opm_productid"=>$f->opm_productid,"filetype"=>$f->filetype,"filesize"=>$f->filesize,"default_imageid"=>$f->default_imageid);

			foreach ($data['seps']->result() as $f)
				$data['files'][] = array("dbtype"=>"Separation","fileid"=>$f->fileid,"filename"=>$f->filename,"productname"=>$f->productname,"opm_productid"=>$f->opm_productid,"filetype"=>$f->filetype,"filesize"=>$f->filesize,"default_imageid"=>$f->default_imageid);
						
			multi2dSortAsc($data['files'], "productname");
			
			$data['p'] = $this->properties_model->fetchPropertyInfo($propertyid);    	
	    	
	    	$template['content'] = $this->load->view('properties/showAllFiles',$data,true);     
	        
	        $template['nav2'] = "<a href=\"".base_url()."properties/search/\">Properties</a>&nbsp;&nbsp;>&nbsp;&nbsp;<a href=\"".base_url()."properties/view/".$data['p']->propertyid."\">" . $data['p']->property . "</a>&nbsp;&nbsp;>&nbsp;&nbsp;View All Files";
	        $template['page_title'] = "Download " . $data['p']->property . " files";
			$template['bigheader'] = "Download " . $data['p']->property . " files";
			
		//	$template['contentNav'] = $this->load->view('properties/contentNav',$data,true);
	
			$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','datepicker'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
			
			$this->load->view('global/maintemplate', $template);
		
		}
    }
    
    function createZipFile() {
    
    	$this->load->model('files_model');
    	
    	if (!$this->input->post("propertyid"))
    		$this->opm->displayError("No Property Specified!");
    
    	$p = $this->properties_model->fetchPropertyInfo($this->input->post("propertyid"));
    
    	//$this->load->library('Zipfile');
    //	die();
    	if (checkPerms('can_view_all_files_list',true)) {

		//$ziper = new Zipfile();
		//$ziper->addDir($p->property);
		
		// now lets create array of filepaths to add!
		
		// let's make a dir to put the files in!
			
		$dirName = mktime();
		
		if (is_array($this->input->post("MFsToDL"))) {
		
			$arrMFKeys = array_keys($this->input->post("MFsToDL"));
			$strMFIDs = implode(",",$arrMFKeys);
			
			// fetch from db
			
			$files = $this->files_model->fetchMasterFiles(null,$strMFIDs);
			
			if (!mkdir($this->config->item('tmpDir') . $dirName))
				$this->opm->displayError("Could not create directory for zip file!");
			
						
			foreach ($files->result() as $f) {
				
				//$data = file_get_contents($this->config->item('fileUploadPath') . "masterfiles/" . $f->fileid);
				//$ziper->addFile($data,$f->filename);

				
				$escFileName = str_replace(" ", "\\ ", $f->filename);
				$systemCall = "cp " . $this->config->item('fileUploadPath') . "masterfiles/" . $f->fileid . " " . $this->config->item('tmpDir') . $dirName . "/" . $escFileName;

				system($systemCall,$strReturn);
			
			}

				
			
		}

		
		
		if (is_array($this->input->post("SEPsToDL"))) {
		
			$arrSEPKeys = array_keys($this->input->post("SEPsToDL"));
			$strSEPIDs = implode(",",$arrSEPKeys);
			
			// fetch from db
			
			$files = $this->files_model->fetchSeparations(null,$strSEPIDs);
			
			foreach ($files->result() as $f) {
				
			//	$data = file_get_contents($this->config->item('fileUploadPath') . "separations/" . $f->fileid);
			//	$ziper->addFile($data,$f->filename);
				
				$escFileName = str_replace(" ", "\\ ", $f->filename);
				$systemCall = "cp " . $this->config->item('fileUploadPath') . "separations/" . $f->fileid . " " . $this->config->item('tmpDir') . $dirName . "/" . $escFileName;

				system($systemCall,$strReturn);
			
			}
		
		}
		
		// okay, now let's tar the whole dir into a web accessible dir
		
		$escProperty = str_replace(" ", "_", $p->property);
		
		$tarFilePath = $this->config->item('tmpDir') . $escProperty."_files_".mktime().".tar";
		
		$x = system("cd ".$this->config->item('tmpDir')." ; tar -cf $tarFilePath " . $dirName . "/", $strReturn);
		
		// now, let's remove the original folder.
		
		$x = system("rm -fR " . $this->config->item('tmpDir') . $dirName . "/");
		
		//die($x);
		
		// now let's spit out the tar file.
		
		
		
		
		header("Content-Type: application/tar");
		header("Content-Disposition: attachment;filename=".$escProperty."_files_".mktime().".tar" );
		header("Content-Length: " . filesize($tarFilePath) ."; ");
		header('Pragma: no-cache');
		header('Expires: 0');
		
		$file = file_get_contents($tarFilePath);
		
		echo $file;
		
		// now lets remove the tar file
		
		$x = system("rm -f " . $tarFilePath);

		// yer done.
		
		exit;
		
		//$ziper->addFile("test.txt","test_dir/test_output.txt"); //array of files
		//$ziper->output($p->property."_files_".mktime().".zip");
		
		//echo "did you do anything?";


    	
    	
    	}
    
    }
    
    function save() {
    
    	//print_r($_FILES);
    	//exit();
    	
    	$this->load->model('products_model');
    	
    	if ($this->input->post('hideAllProducts')) {
    	
    		if (checkPerms('can_hide_all_products')) {
    		
    			$propertyid = $this->input->post('propertyid');
    			
    			if ($this->products_model->hideAllProducts($propertyid)) {
    			
    				$this->opm->displayAlert("All Products Hidden From External Users!","/properties/view/" . $propertyid);
					return true;
    			
    			} else {
    			
    				$this->opm->displayError("Property Image must be 300x100!");
					return false;
    			
    			}
 
    		}
    	
    	}
    	
    	if (checkPerms('can_add_properties',true)) {
        		
    		$errors = "";
    	
			$postdata['propertyid'] = $this->input->post('propertyid');

			if (!$postdata['property'] = $this->input->post('property'))
				$errors .= "Property Must have a Name!<br />";
			
			$postdata['approval_methodid'] = $this->input->post('approval_methodid');
			$postdata['copyright'] = $this->input->post('copyright');
			$postdata['default_productdesc'] = $this->input->post('default_productdesc');
			$postdata['isactive'] = $this->input->post('isactive');
			$postdata['isharley'] = $this->input->post('isharley');
			$postdata['nv_propid'] = $this->input->post('nv_propid');
			
			// get original property info to track changes...
			
			if ($postdata['propertyid']) {
				$origPropertyInfo = $this->properties_model->fetchPropertyInfo($postdata['propertyid']);
			
			// if we've made changes to approval status method, we need to update approval status for all prods
			
				if ($origPropertyInfo->approval_methodid != $postdata['approval_methodid'])
					$this->opm->updateApprovalStatusProperty($postdata['propertyid']);
			
			}
			
			if (!$postdata['propertyid'] || ($postdata['nv_propid'] != $origPropertyInfo->nv_propid)) { // this is an add, or we changed nv_propid... must make sure it doesn't conflict with other nv_propids.
			
				
				/*if ($this->properties_model->checkForDuplicateNavisionPropID($postdata['nv_propid'])) {
				
					$errors .= "Navision PropertyID already exists in system<br />";
				
				}*/
			
			}
			
			if ($errors) {
			
				$this->opm->displayError($errors);
				return false;
			
			}
		
			// first, save avatar upload if exists
			
			if (is_uploaded_file($_FILES['propertyImage']['tmp_name'])) { // we have a file upload!
			
				// first delete any old property image!
				
				$this->properties_model->deletePropertyImage($postdata['propertyid']);
			
				// upload avatar
				
				$config['upload_path'] = $this->config->item('fileUploadPath') . "/propertyimages/";
				
				//die($config['upload_path']);
				
				$config['allowed_types'] = 'gif|jpg|png';
				$config['max_size'] = '10000';
				$config['max_width'] = '300';
				$config['max_height'] = '100';
				$config['encrypt_name'] = true;
		
				$this->load->library('upload', $config);
				
				if ($this->upload->do_upload('propertyImage')) {
				
					$arrUpload = $this->upload->data();
					
					if ($arrUpload['image_width'] != 300 || $arrUpload['image_height'] != 100) {
					
						/*echo "<pre>";
						print_r($this->upload->data());
						echo "</pre>";
						die();*/
					
						$this->opm->displayError("Property Image must be 300x100!");
						return false;
					
					}
				
				} else {
				
					die($this->upload->display_errors());
				}
			
			
			}
			
					
			if (isset($arrUpload)) // if we've had an upload, set the avatar path.
				$postdata['image_path'] = $arrUpload['file_name'];
	
			
			if ($propertyid = $this->properties_model->savePropertyInfo($postdata)) {
			
				
				$this->opm->displayAlert("Property Has Been Saved!","/properties/view/" . $propertyid);
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the property!");
				return true;
			}
		
		}
				
	}
	
	function saveBilling() {
    
    	if (checkPerms('can_edit_properties_billing_info',true)) {

			$postdata['propertyid'] = $this->input->post('propertyid');
			$postdata['channelPercentage'] = $this->input->post('channelPercentage');

			if ($this->properties_model->savePropertyBillingInfo($postdata)) {
			
				
				$this->opm->displayAlert("Property billing info Has Been Saved!","/properties/view/" . $postdata['propertyid'] . "/billing");
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the info!");
				return true;
			}
		
		}		
				
	}
	
	function saveProductLines() {
	
		if (checkPerms('can_edit_productlines',true)) {
		
			$errors = "";
	
			$this->load->model('productlines_model');
			
			// err checking
			
			if (!$postdata['propertyid'] = $this->input->post('propertyid'))
				$errors .= "Unknown Error<br />";
				
			if (!$postdata['arrProductLineIDs'] = $this->input->post('arrProductLineIDs'))
				$errors .= "Bad Data<br />";
			
			$postdata['isactive'] = $this->input->post('isactive');
			
			
			
			if ($errors) {
		
				$this->opm->displayError($errors);
				return false;
			
			}
				
			if ($this->productlines_model->saveProductLines($postdata)) {
				
				$this->opm->displayAlert("Product Lines Have Been Saved!","/properties/view/" . $postdata['propertyid']);
				return true;	
			
			} else {
			
				$this->opm->displayError("There was an error saving the Product Lines");
				return true;
			
			}
		
		}
	
	}
	
	function addProductLine() {
		
		$this->load->model('productlines_model');
	
		if (checkPerms('can_add_product_lines',true)) {
		
			$errors = "";
		
			if (!$postdata['propertyid'] = $this->input->post('propertyid'))
				$errors .= "Unknown Error<br />";
				
			if (!$postdata['productline'] = $this->input->post('productline'))
				$errors .= "It appears that you did not enter a product line.<br />";
			
			if ($errors) {
		
				$this->opm->displayError($errors);
				return false;
			
			}
				
			if ($this->productlines_model->addProductLine($postdata['propertyid'],$postdata['productline'])) {
				
				$this->opm->displayAlert("Product Lines Have Been Saved!","/properties/view/" . $postdata['propertyid']);
				return true;	
			
			} else {
			
				$this->opm->displayError("There was an error saving the Product Lines");
				return true;
			
			}

		}
	
	}
    
    function loadContent($id,$tabName)
    
    {
    
    	if ($tabName == 'basicinfo') {
			
			$data['mode'] = "edit";
			$this->load->model('approvalmethods_model');
    		$data['appMethods'] = $this->approvalmethods_model->fetchApprovalMethods();
    	
    	} 
    	
    	if ($tabName == 'productLines') {
			
			$this->load->model('productlines_model');
    		$data['productLines'] = $this->productlines_model->fetchProductLines($id,true);
    	
    	}
    	
    	if ($tabName == 'assets') {
			
    		$data['assets'] = $this->properties_model->fetchAssets($id);
    	
    	}
    	
    	if ($tabName == 'genres') {
			
			$this->load->model('genres_model');
			
    		$data['genres'] = $this->genres_model->fetchGenres(false,0,null,$id);
    	
    	}
    	
    	if ($tabName == 'billing') {
			
			$this->load->model('invoices_model');
    		$data['channels'] = $this->invoices_model->fetchChannels($id);
    	
    	}
    	
       	$data['p'] = $this->properties_model->fetchPropertyInfo($id);
		echo $this->load->view('properties/'.$tabName,$data,true);
		
    
    }
    
    function saveAsset($propertyid) {
    
    	/*echo "<pre>";
    	print_r($_POST);
    	die();*/
    	
    	if (checkPerms('can_upload_assets',true)) { // check permissions
			
			$errors = "";	
		
			$postdata['propertyid'] = $this->input->post('propertyid');
			$postdata['random_str'] = $this->input->post('random_str');
			
			if (!$postdata['assetName'] = $this->input->post('assetName'))
				$errors .= "Asset Must have a Name!<br />";
			
			//if (!$postdata['assetDetail'] = $this->input->post('assetDetail'))
			//	$errors .= "Asset Must have details!<br />";
				
			if (!$postdata['assetTypeId'] = $this->input->post('assetTypeId'))
				$errors .= "Asset Must have a type!<br />";
			
			if ($errors) {
		
				$this->opm->displayError($errors);
				return false;
			
			}
			
			$postdata['assetDetail'] = $this->input->post('assetDetail');
			
			if (is_uploaded_file($_FILES['assetThumbnail']['tmp_name'])) { // we have a file upload!
				
				$sizeInfo = getimagesize($_FILES['assetThumbnail']['tmp_name']);
				
				$postdata['image'] = fread(fopen($_FILES['assetThumbnail']['tmp_name'], "r"), filesize($_FILES['assetThumbnail']['tmp_name']));
				$postdata['filename'] = $_FILES['assetThumbnail']['name'];
				$postdata['image_type'] = $_FILES['assetThumbnail']['type'];

				// resize image to 75 wide
				
				$tempfilepath = $this->config->item('fileUploadPath') . "assets/tempthumbnail";
				
				$size = 75;  // new image width
				$src = imagecreatefromstring($postdata['image']); 
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
				  
				imagecopyresampled($img,$src,0,0,0,0,$new_w,$new_h,$width,$height);
				
				// determine image type and write it to file.
				
				if ($postdata['image_type'] == "image/pjpeg" || $postdata['image_type'] == "image/jpeg") {    
					imagejpeg($img,$tempfilepath); 
				} else if ($postdata['image_type'] == "image/x-png") {
					imagepng($img,$tempfilepath);
				} else if ($postdata['image_type'] == "image/gif") {
					imagegif($img,$tempfilepath);
				}
				
				//die
				
				// read temp file!
				
				$postdata['resizedThumbnail'] = fread(fopen($tempfilepath, "r"), filesize($tempfilepath));
				
				//unlink($_FILES['assetThumbnail']['tmp_name']);
				//unlink($tempfilepath);
				unset($postdata['image']);
				
				// read temp file!
				
				$postdata['resizedThumbnail'] = fread(fopen($tempfilepath, "r"), filesize($tempfilepath));

				$file = $_FILES['assetThumbnail']['tmp_name'];
				
				$destDir = $this->config->item('fileUploadPath') . "assets/" . $postdata['random_str'];
				
				if (move_uploaded_file($file, $destDir)) {
				
				}
								
				
			} 
			
	
			
			if ($this->properties_model->saveAsset($postdata)) {
			
				
				$this->opm->displayAlert("Asset Has Been Saved","/properties/view/" . $postdata['propertyid'] . "/assets");
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the asset!");
				return true;
			}
			
		
		}		
			
	}
	
	function exportPropertyExcel() {
	
		$this->opm->createPropertyExcel();
	
	}
}
?>