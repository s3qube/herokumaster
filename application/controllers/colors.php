<?php

class Colors extends CI_Controller {

	function __construct() {
    
    	parent::__construct();
    	$this->opm->checkLogin();
    	
    	$this->load->helper('text');
    	$this->opm->opmInit();
		
		$this->load->model('colors_model');

    }

    function addColorToProduct() {
		
		if (checkPerms("can_add_available_colors",true)) { 
		
			$data = array();
	
			$colorid = $this->input->post('colorid');
			$opm_productid = $this->input->post('opm_productid');
			
			$color = $this->colors_model->fetchColor($colorid);
			
			if ($this->colors_model->addColorToProduct($opm_productid,$colorid)) {
				
				$message = $color->color . " added to colors by " . $this->userinfo->username;
				$this->opm->addHistoryItem($this->input->post('opm_productid'),$message);
				$this->opm->setLastModified($opm_productid);
				
				// try to regenerate SKUs!
				
				if ($this->opm->buildSkusForProduct($opm_productid,true)) {
				
					$this->opm->displayAlert("Color Added To Product and SKUs updated!","/products/view/" . $opm_productid);
					return true;
				
				} else {
				
					$this->opm->displayAlert("Color Added To Product but SKUs could not be updated!","/products/view/" . $opm_productid);
					return true;
					
				}
				
			} else {
			
				$this->opm->displayError("Color could not be added!");
	       		return false;
			
			}
			
		} 
		
		
	}
	
	function removeColor($opm_productid,$colorid) {
		
		if (checkPerms("can_add_available_colors",true)) { 
		
			$data = array();
			
			if (!$color = $this->colors_model->fetchColor($colorid)) {
			
				$this->opm->displayError("Color could not be removed!");
	       		return false;
			
			}
			
			if ($this->colors_model->removeColorFromProduct($opm_productid,$colorid)) {
				
				$message = $color->color . " removed from colors by " . $this->userinfo->username;
				$this->opm->addHistoryItem($opm_productid,$message);
				$this->opm->setLastModified($opm_productid);
				
				if ($this->opm->buildSkusForProduct($opm_productid,true)) {
				
					$this->opm->displayAlert("Color Removed From Product and SKUs updated!","/products/view/" . $opm_productid);
					return true;
				
				} else {
				
					$this->opm->displayAlert("Color Removed From Product, but SKUs could not be updated.","/products/view/" . $opm_productid);
					return true;
					
				}
			
			} else {
			
				$this->opm->displayError("Color could not be removed!");
	       		return false;
			
			}
			
		} 
		
		
	}

	


}
?>