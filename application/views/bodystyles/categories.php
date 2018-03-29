<?php
class Categories extends Controller {

	function __construct()
    {
    	parent::Controller();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('categories_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'categories';
    	
    	global $searchArgList;
    	$searchArgList = array("searchText","firstLetter");
    	
    }

	function index() // index will be the property list.
	{
	
		redirect("/categories/search");	

	}
	
	function search($searchText = 0, $firstletter = null) // index will be the property list.
	{
		
		$data['firstLetters'] = $this->categories_model->fetchFirstLetterLinks($firstletter);
	
		$data['totalCategories'] = $this->categories_model->fetchListCategories(true,true,true,$searchText,0,0,$firstletter);
		$data['categories'] = $this->categories_model->fetchListCategories(true,true,false,$searchText,0,0,$firstletter);
		$template['nav2'] = "Search Categories";
		
		//$config['full_tag_open'] = '<p>';
		//$config['full_tag_close'] = '</p>';
		
		$template['page_title'] = "View Categories";
		$template['bigheader'] = "Search Categories";
		$template['searchArea'] = $this->load->view('categories/searchArea',$data,true);
		$template['rightNav'] = $this->load->view('categories/rightNav',$data,true);
		$template['contentNav'] = $this->load->view('categories/searchNav',$data,true);
		$template['content'] = $this->load->view('categories/search', $data,true);
		
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
		
		$url = "/categories/search/";
		
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
    		
    	$data['p'] = $this->categories_model->fetchCategoryInfo($id);
    	
    	$template['content'] = '';
     	$template['headInclude'] = $this->load->view('categories/loadContentJS',$data,true);;
     
        
        $template['nav2'] = "<a href=\"".base_url()."categories/search/\">Categories</a>&nbsp;&nbsp;>&nbsp;&nbsp;" . $data['p']->category;
        $template['page_title'] = "View Category - " . $data['p']->category;
		$template['bigheader'] = $data['p']->category;
		$template['contentNav'] = $this->load->view('categories/contentNav',$data,true);

		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','datepicker'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		
		$this->load->view('global/maintemplate', $template);
    }
    
    function add()
    {
		
		$data = array();
		
		$this->load->model('approvalmethods_model');
    	$data['appMethods'] = $this->approvalmethods_model->fetchApprovalMethods();
		
    	$template['content'] = $this->load->view('categories/basicinfo',$data,true);
     	//$template['headInclude'] = $this->load->view('properties/loadContentJS',$data,true);
     
        
        $template['nav2'] = "Add New Category";
        $template['page_title'] = "Add New Category";
		$template['bigheader'] = "Add New Category";
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
			
			foreach ($data['mfs']->result() as $f)
				$data['files'][] = array("dbtype"=>"Masterfile","fileid"=>$f->fileid,"filename"=>$f->filename,"productname"=>$f->productname,"filetype"=>$f->filetype,"filesize"=>$f->filesize);
			
			foreach ($data['seps']->result() as $f)
				$data['files'][] = array("dbtype"=>"Separation","fileid"=>$f->fileid,"filename"=>$f->filename,"productname"=>$f->productname,"filetype"=>$f->filetype,"filesize"=>$f->filesize);
			
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
    	
    	
    	
    	if (checkPerms('can_add_properties',true)) {
    	
    		$errors = "";
    	
			//$postdata['propertyid'] = $this->input->post('propertyid');

			if (!$postdata['category'] = $this->input->post('category'))
				$errors .= "Category Must have a Name!<br />";
			
			//$postdata['approval_methodid'] = $this->input->post('approval_methodid');
			//$postdata['copyright'] = $this->input->post('copyright');
			
			
			if ($errors) {
			
				$this->opm->displayError($errors);
				return false;
			
			}
		
			// first, save avatar upload if exists
			
			
			
			if ($propertyid = $this->properties_model->savePropertyInfo($postdata)) {
			
				
				$this->opm->displayAlert("Property Has Been Saved!","/categories/view/" . $propertyid);
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the property!");
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
    	
       	$data['p'] = $this->properties_model->fetchPropertyInfo($id);
		echo $this->load->view('properties/'.$tabName,$data,true);
		
    
    }
    
    function saveAsset($propertyid) {
    	
    	if (checkPerms('can_upload_assets',true)) { // check permissions
			
			$errors = "";	
		
			$postdata['propertyid'] = $this->input->post('propertyid');
			$postdata['random_str'] = $this->input->post('random_str');
			
			if (!$postdata['assetName'] = $this->input->post('assetName'))
				$errors .= "Asset Must have a Name!<br />";
			
			if (!$postdata['assetDetail'] = $this->input->post('assetDetail'))
				$errors .= "Asset Must have details!<br />";
			
			if ($errors) {
		
				$this->opm->displayError($errors);
				return false;
			
			}
			
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
				
				// determine image type and send it to the client    
				if ($postdata['image_type'] == "image/pjpeg" || $postdata['image_type'] == "image/jpeg") {    
					imagejpeg($img,$tempfilepath); 
				} else if ($postdata['image_type'] == "image/x-png") {
					imagepng($img,$tempfilepath);
				} else if ($postdata['image_type'] == "image/gif") {
					imagegif($img,$tempfilepath);
				}
				
				// read temp file!
				
				$postdata['resizedThumbnail'] = fread(fopen($tempfilepath, "r"), filesize($tempfilepath));
				
				//////////////////////////////////////////////////////////////
				//random_str(20);
				$file=$_FILES['assetThumbnail']['tmp_name'];
				$destDir = $this->config->item('fileUploadPath') . "assets/" . $postdata['random_str'];
				if (move_uploaded_file($file, $destDir)) {
				
				}
				//////////////////////////////////////////////////////////////
				
				
				//unlink($_FILES['assetThumbnail']['tmp_name']);
				//unlink($tempfilepath);
				//unset($postdata['image']);
				
				//header("Content-type: " . $postdata['image_type']);
				//echo $postdata['resizedThumbnail'];
				//exit();
				
				
				
				
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
	
	function editProductLine($productlineid,$propertyid)
	{
	
	$data = array();
		
		//$this->load->model('approvalmethods_model');
    	//$data['appMethods'] = $this->approvalmethods_model->fetchApprovalMethods();
		$this->load->model('productlines_model');
		$data['eproductline']=$this->productlines_model->fetchProductLine($productlineid);
		$data['propertyid']=$propertyid;
		
		
    	$template['content'] = $this->load->view('properties/editproductline',$data,true);
     	//$template['headInclude'] = $this->load->view('properties/loadContentJS',$data,true);
     
        
        $template['nav2'] = "Edit ProductLine";
        $template['page_title'] = "Edit ProductLine";
		$template['bigheader'] = "Edit ProductLine";
		//$template['contentNav'] = $this->load->view('properties/contentNav',$data,true);

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
	
	
		
			
				
			if ($this->productlines_model->deleteProductLine($productlineid)) {
				
				$this->opm->displayAlert("Product Lines Have Been Deleted!","/properties/view/" . $propertyid);
				return true;	
			
			} else {
			
				$this->opm->displayError("There was an error delete the Product Lines");
				return true;
			
			}

		
	
	}
}
?>