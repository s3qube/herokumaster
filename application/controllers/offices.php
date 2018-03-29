<?php

class Offices extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('offices_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'administration';
    	
    }
	
	function showall() {
	
		$data['offices'] = officeArray2ul($this->offices_model->fetchOffices(),false,true);

   		$template['page_title'] = "Manage Offices";
   		$template['bigheader'] = "Manage Offices";
    	$template['nav2'] = $this->load->view('users/nav2',$data,true);

    	
    	$template['headInclude'] = '<link rel="stylesheet" type="text/css" href="'.base_url().'resources/mktree.css">';
    	$template['rightNav'] = $this->load->view('offices/rightNav',$data,true);
    	$template['content'] = $this->load->view('offices/showall',$data,true);
    	
    	$arrJS['scripts'] = array('mktree','opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    
    }
    
    function add()
   	{
    	
    	$data['offices'] = officeArray2Select($this->offices_model->fetchOffices());
    	$template['content'] = $this->load->view('offices/add',$data,true);
        
        $template['page_title'] = "Add Office";
        $template['bigheader'] = "Add Office";
       	$template['nav2'] = "Add Office";
        
		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    function edit($id)
   	{
    
    	$data['office'] = $this->offices_model->fetchOffice($id);
    	$data['id'] = $id;
    	
    	// print_r($data['office']->permissions);
    	// exit();
    	
    	$template['content'] = $this->load->view('offices/edit',$data,true);
        
        $template['page_title'] = "Edit Office";
        $template['bigheader'] = "Edit Office :: " . $data['office']->office;
       	$template['nav2'] = $this->load->view('users/nav2',$data,true);
        
		$arrJS['scripts'] = array('jquery-1.6.2.min','opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    
    function assignProperties($officeID = 0)
   	{
   	
   		if (checkPerms('can_assign_properties',true)) {
   		
   			$this->load->model('properties_model');
    		
    		$data = array();
    		
    		$data['officeID'] = $officeID;
    		
    		$data['offices'] = officeArray2Select($this->offices_model->fetchoffices());

    		$data['properties'] = $this->properties_model->fetchPropertiesWithUGAssignments(false,$officeID);
    		
	    	//$data['office'] = $this->offices_model->fetchoffice($id);
	    	//$data['id'] = $id;
	    	
	    	// print_r($data['office']->permissions);
	    	// exit();
	    	
	    	$template['content'] = $this->load->view('offices/assignProperties',$data,true);
	        
	        $template['page_title'] = "Assign Properties to offices";
	        $template['bigheader'] = "";
	       	$template['nav2'] = $this->load->view('users/nav2',$data,true);
	        
			$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
		
			$this->load->view('global/maintemplate', $template);
			
		}
    
    }
    
    function savePropertyAssignments() {
    	
    	//print_r($_POST);
    	//die();
    
    	$errors = '';
    
    	if (!$officeID = $this->input->post('officeID'))
				$errors .= "office must have a value!<br />";

		if ($errors) {
	
			$this->opm->displayError($errors);
			return false;
		
		}	
    	
    	$propertyIDs = $this->input->post('propertyIDs');
 
    	$this->offices_model->saveofficeProperties($officeID, $propertyIDs);
    	
    	$this->opm->displayAlert("office Properties have been saved!",'/offices/assignProperties/'.$officeID.'/');
		return true;
    	
    
	}
    
    
    function loadContent($id,$tabName)
    
    {
    
    	if ($tabName == 'basicinfo') {
    		$this->load->model('users_model');
    		$this->load->model('offices_model');
    		
    		$data['offices'] = officeArray2Select($this->offices_model->fetchoffices());
    		//$data['product'] = $this->products_model->fetchProductInfo($id);
    	
    	}
    		
    	$data['user'] = $this->users_model->fetchUserInfo($id);
		echo $this->load->view('users/'.$tabName,$data,true);
		
    
    }
    
    function save($id) {
    	
    	//print_r($_POST);
    	//die();
    
    	$errors = '';
    
    	if (!$office = $this->input->post('office'))
				$errors .= "Office must have a value!<br />";

		if ($errors) {
	
			$this->opm->displayError($errors);
			return false;
		
		}	
    	
    	$this->opm->displayAlert("Office has been saved!",'/offices/showall/');
		return true;
    	
    	//redirect( , 'location'); 
    
	}
	
	function doAdd() {
    
    	$errors = '';
    
    	if (!$office = $this->input->post('office'))
				$errors .= "Office must have a value!<br />";

		if ($errors) {
	
			$this->opm->displayError($errors);
			return false;
		
		}	
    	
    	$parentOfficeId = $this->input->post('parentOfficeId');
    	
    	$this->offices_model->addoffice($office, $parentOfficeId);
    	
    	$this->opm->displayAlert("Office has been added!",'/offices/showall/');
		return true;
    
	}
    
    
    
}


?>