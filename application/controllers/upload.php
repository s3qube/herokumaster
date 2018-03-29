<?php
class Upload extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->load->model('images_model');
    }

	function showUpload($opm_productid,$fileType) 
	{
		// if we are already logged in, redirect to start page

		$template['page_title'] = "Upload";
		$template['opm_productid'] = $opm_productid;
		$template['fileType'] = $fileType;

		$this->load->view('upload', $template);
	}
	
	function showJavaUpload($opm_productid,$fileType) 
	{
		// if we are already logged in, redirect to start page

		$template['page_title'] = "Upload";
		$template['opm_productid'] = $opm_productid;
		$template['fileType'] = $fileType;

		$this->load->view('uploadJava', $template);
	}
	
	function showAssetUpload($propertyid) 
	{
        
		$this->opm->activeNav = 'properties';
		$this->load->model('properties_model');
		$this->load->model('assettypes_model');
		
		$data['assetTypes'] = $this->assettypes_model->fetchAssetTypes();

		
		// we need a random string to associate file upload with name, info, thumbnail, etc.
		
		$data['random_str'] = random_str(20);
    
    	$data['p'] = $this->properties_model->fetchPropertyInfo($propertyid);
     	
     	//$template['headInclude'] = $this->load->view('product/loadContentJS',$data,true);
       
        $template['page_title'] = "Upload New Asset";
        $template['bigheader'] = "Upload New Asset for " . $data['p']->property;
        $template['nav2'] = $data['p']->property . "&nbsp;&nbsp;&gt;&nbsp;&nbsp;" . "Upload New Asset";
       	// $template['contentNav'] = $this->load->view('product/contentNav',$data,true);
       	// $template['rightNav'] = $this->load->view('product/rightNav',$data,true);
        
        $template['content'] = $this->load->view('uploadAsset', $data,true);
        
        $arrJS['scripts'] = array('jquery-1.6.2.min','jquery.validate-1.8.1.min','opm_scripts','datepicker','plupload','jquery.plupload.queue','plupload.html5');
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
		// asset upload page should never be cached!
		
		header("cache-Control: no-store, no-cache, must-revalidate");
        header("cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	
		$this->load->view('global/maintemplate', $template);


		
	}
	
	function doUpload() {
	
		print_r($_POST);
		die("that's all");
		
	}
	
	function handleHtml5Upload() {
	
		$str = 'File was uploaded successfully!';
		echo json_encode(array('status'=>$str));
		die();
	
	}
	

}
?>