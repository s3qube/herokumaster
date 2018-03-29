<?php
class Tooltips extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	//$this->load->model('users_model');
    }

	
	function searchTip($imageid) {
		
		$template['imageid'] = $imageid;
		$this->load->view('tooltips/imageonly', $template);
	
	}
	
	function grabsheetTip($imageid) {
		
		$this->load->model('products_model');
		
		
		$template['product'] = $this->products_model->fetchProductInfoByImageID($imageid);
		
		$template['imageid'] = $imageid;
		$this->load->view('tooltips/standard', $template);
	
	}
	
}
?>