<?php
class Assets extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('assets_model');
    	$this->load->model('properties_model');
    	$this->load->model('authors_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'assets';
    	
    	global $searchArgList;
    	$searchArgList = array("propertyid","authorid","tags","perPage","offset");
    	
    }

	function index() // index will be the asset list.
	{
	
		redirect("/assets/search");	

	}
	
	function search($propertyid = 0, $authorid = 0, $tags = null, $perPage = 0, $offset = 0) // index will be the asset list.
	{
		
		//die("hi");
		global $searchArgList;
		
		$tags = urldecode($tags);
		
		foreach ($searchArgList as $k=>$d) 
    		$data['args'][$d] = ${$d}; 
    		
    	//print_r($data['args']);
		
		//$data['firstLetters'] = $this->assets_model->fetchFirstLetterLinks($firstLetter);
	
		
		$template['nav2'] = "Search Assets";
		
		//$config['full_tag_open'] = '<p>';
		//$config['full_tag_close'] = '</p>';
		
		$template['page_title'] = "View Assets";
		$template['bigheader'] = "Search Assets";
		
		$data['properties'] = $this->properties_model->fetchProperties();
		$data['authors'] = $this->authors_model->fetchAuthors();

		
		$template['searchArea'] = $this->load->view('assets/searchArea',$data,true);
		$template['rightNav'] = $this->load->view('assets/rightNav',$data,true);
		
		
		$data['totalAssets'] = $this->assets_model->fetchAssets(true,null,null,$propertyid,$authorid,$tags);
		
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/assets/search/'.$propertyid."/".urlencode($tags)."/".$perPage."/";
		$config['total_rows'] = $data['totalAssets'];
		
		// determine per page
		
		if ($perPage)
			$config['per_page'] = $perPage;
		else
			$config['per_page'] = $this->config->item('searchPerPage');
		
		$config['uri_segment'] = 5;


    	$this->pagination->initialize($config);
	
		$data['assets'] = $this->assets_model->fetchAssets(false,$offset,$config['per_page'],$propertyid,$authorid,$tags);	

		$data['prodStart'] = $offset + 1;
    	$data['prodEnd'] = $data['prodStart'] + ($data['assets']->num_rows() - 1);

		$template['content'] = $this->load->view('assets/search', $data,true);
		
		$template['contentNav'] = $this->load->view('assets/searchNav',$data,true);

		
        $arrJS['scripts'] = array('jquery-1.8.1.min','opm_scripts','chosen.jquery.min','cloudzoom'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
        
		$this->load->view('global/maintemplate', $template);
	
	}
	
	function ajaxSearchResults($propertyid = 0, $tags = null, $perPage = 0, $offset = 0) {
		
			
		
		
		
	}
	
	function submit() { // construct a search url from a form submission
    
    	global $searchArgList;

		foreach ($searchArgList as $key=>$data) {
			
			if ($this->input->post($data))
				$segments[$data] = $this->input->post($data);
			else
				$segments[$data] = 0;	
			
		}
		
		$url = "/assets/search/";
		
		foreach ($segments as $data)
			$url .= $data . "/";
			
		
		redirect($url);
			
	
    
    }
	
	
	
	function edit($id = 0)
    {
    	
    	if ($id > 0) { // this is an edit
    		
    		$data['mode'] = "edit";
    		$data['a'] = $this->assets_model->fetchAssetInfo($id);
    	
    	} else { // this is an add
	    	
	    	$data['mode'] = "add";
	    	$data['a']->assetid = 0;
	    	$data['a']->assetname = "";
    	}
    	
    	$data['authors'] = $this->authors_model->fetchAuthors();
    	
    	$template['content'] = $this->load->view('assets/edit',$data,true);
     	//$template['headInclude'] = $this->load->view('assets/loadContentJS',$data,true);;
     
        
        $template['nav2'] = "<a href=\"".base_url()."assets/search/\">Assets</a>&nbsp;&nbsp;>&nbsp;&nbsp;" . $data['a']->assetname;
        
        if ($data['mode'] == 'add') {
		
			$template['bigheader'] = "Create New Asset";
       		$template['page_title'] = "Create New Asset";
       
        } else {
		
			$template['bigheader'] = $data['a']->assetname;
       		$template['page_title'] = "View asset - " . $data['a']->assetname;
	        
        }


		$arrJS['scripts'] = array('jquery-1.8.1.min','opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		
		$this->load->view('global/maintemplate', $template);
   
    }
    
    function upload()
    {
    	
    	$data['properties'] = $this->properties_model->fetchProperties();
    	$data['authors'] = $this->authors_model->fetchAuthors();
    	
    	$template['content'] = $this->load->view('assets/upload',$data,true);
     	//$template['headInclude'] = $this->load->view('assets/loadContentJS',$data,true);;
     
        
        $template['nav2'] = "Upload Assets";

		$template['bigheader'] = "Upload Assets";
       	$template['page_title'] = "Upload Assets";
       



		$arrJS['scripts'] = array('jquery-1.8.1.min','opm_scripts','chosen.jquery.min','plupload','jquery.plupload.queue','plupload.html5'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		
		$this->load->view('global/maintemplate', $template);
   
    }
        
      
    function save() {
    	
    	//echo "<pre>";
    	//print_r($_POST);
    	//exit();
    
    	
    	if (checkPerms('can_edit_assets',true)) {
        		
    		$errors = "";
    	
			$postdata['assetid'] = $this->input->post('assetid');

			//if (!$postdata['asset'] = $this->input->post('asset'))
		//		$errors .= "Asset Must have a Name!<br />";
			
			$postdata['authorid'] = $this->input->post('authorid');
			$postdata['assetName'] = $this->input->post('assetname');
			$postdata['assetDetail'] = $this->input->post('assetDetail');
			$postdata['tags'] = $this->input->post('tags');
			
			
			if ($assetid = $this->assets_model->saveAssetInfo($postdata)) {
			
				
				$this->opm->displayAlert("asset Has Been Saved!","/assets/edit/" . $assetid);
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the asset!");
				return true;
			}
		
		}
				
	}
	
	    
    function saveAsset($assetid) {
    
    	/*echo "<pre>";
    	print_r($_POST);
    	die();*/
    	
    	if (checkPerms('can_upload_assets',true)) { // check permissions
			
			$errors = "";	
		
			$postdata['assetid'] = $this->input->post('assetid');
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
			
	
			
			if ($this->assets_model->saveAsset($postdata)) {
			
				
				$this->opm->displayAlert("Asset Has Been Saved","/assets/view/" . $postdata['assetid'] . "/assets");
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the asset!");
				return true;
			}
			
		
		}		
			
	}

}
?>