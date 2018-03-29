<?php

class Sizes extends CI_Controller {

	function __construct() {
    
    	parent::__construct();
    	$this->opm->checkLogin();
    	
    	$this->load->helper('text');
    	$this->opm->opmInit();
		
		$this->load->model('sizes_model');

    }

    function saveSizes() {
		
		if (checkPerms("can_add_available_sizes",true)) { 
		
			$data['opm_productid'] = $this->input->post('opm_productid');
			
			if ($this->sizes_model->saveSizes($data['opm_productid'],$this->input->post('sizes'))) {
				
				$data['success'] = true;
				
				$this->opm->setLastModified($data['opm_productid']);
				
				// try to generate SKUs
				
				if ($this->opm->buildSkusForProduct($data['opm_productid'],true))
					$this->session->set_flashdata('alert', "Sizes And SKUs Updated!");
				else
					$this->session->set_flashdata('alert', "Sizes Updated but SKUs could not be updated.");
				
				$this->load->view('pickSizes', $data);
			
			} else {
			
				$data['success'] = false;
				$this->load->view('pickSizes', $data);
			
			}
		
			
		} 
		
		
	}
	
	function pickSizes($opm_productid) {
		
		if (checkPerms("can_add_available_sizes",true)) { 
		
			$data = array();
			$data['opm_productid'] = $opm_productid;
			
			if (!$data['sizes'] = $this->sizes_model->fetchSizes($opm_productid)) {
			
				$this->opm->displayError("Could not fetch sizes!");
	       		return false;
			
			}
			
			$this->load->view('pickSizes', $data);
			
			
			
		} 
		
		
	}

	


}
?>