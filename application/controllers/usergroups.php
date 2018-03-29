<?php

class Usergroups extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('usergroups_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'administration';
    	
    }
	
	function showall() {
	
		$data['usergroups'] = array2ul($this->usergroups_model->fetchUsergroups(),false,true);

   		$template['page_title'] = "Manage Usergroups";
   		$template['bigheader'] = "Manage Usergroups";
    	$template['nav2'] = $this->load->view('users/nav2',$data,true);

    	
    	$template['headInclude'] = '<link rel="stylesheet" type="text/css" href="'.base_url().'resources/mktree.css">';
    	$template['rightNav'] = $this->load->view('usergroups/rightNav',$data,true);
    	$template['content'] = $this->load->view('usergroups/showall',$data,true);
    	
    	$arrJS['scripts'] = array('mktree','opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    
    }
    
    function add()
   	{
    	
    	$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups());
    	$template['content'] = $this->load->view('usergroups/add',$data,true);
        
        $template['page_title'] = "Add Usergroup";
        $template['bigheader'] = "Add Usergroup";
       	$template['nav2'] = "Add Usergroup";
        
		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    function edit($id)
   	{
    
    	$data['usergroup'] = $this->usergroups_model->fetchUsergroup($id);
    	$data['id'] = $id;
    	
    	// print_r($data['usergroup']->permissions);
    	// exit();
    	
    	$template['content'] = $this->load->view('usergroups/edit',$data,true);
        
        $template['page_title'] = "Edit Usergroup";
        $template['bigheader'] = "Edit Usergroup :: " . $data['usergroup']->usergroup;
       	$template['nav2'] = $this->load->view('users/nav2',$data,true);
        
		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    
    function assignProperties($usergroupID = 0)
   	{
   	
   		if (checkPerms('can_assign_properties',true)) {
   		
   			$this->load->model('properties_model');
    		
    		$data = array();
    		
    		$data['usergroupID'] = $usergroupID;
    		
    		$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups(null,null,false, true,true));

    		$data['properties'] = $this->properties_model->fetchPropertiesWithUGAssignments(false,$usergroupID);
    		
	    	//$data['usergroup'] = $this->usergroups_model->fetchUsergroup($id);
	    	//$data['id'] = $id;
	    	
	    	// print_r($data['usergroup']->permissions);
	    	// exit();
	    	
	    	$template['content'] = $this->load->view('usergroups/assignProperties',$data,true);
	        
	        $template['page_title'] = "Assign Properties to Usergroups";
	        $template['bigheader'] = "";
	       	$template['nav2'] = $this->load->view('users/nav2',$data,true);
	        
			$arrJS['scripts'] = array('jquery-1.6.2.min','opm_scripts','chosen.jquery.min'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
		
			$this->load->view('global/maintemplate', $template);
			
		}
    
    }
    
    function delete($usergroupID = 0) {
    
   	
   		if (checkPerms('can_delete_usergroups',true)) {
   		
   			
   		
	   			$this->load->model('properties_model');
	    		
	    		$data = array();
	    		
	    		$data['usergroupID'] = $usergroupID;
	    		$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups());
		    	
		    	
		    	if ($this->input->post('usergroupID')) {
		    	
		    		$usergroupID = $this->input->post('usergroupID');
				
					$errors = $this->usergroups_model->checkDelete($usergroupID); 
					
					if (sizeof($errors) > 0) { 
						
						$data['errors'] = $errors;
					
					} else { // no errors encountered… delete it.
					
					
						if ($this->usergroups_model->deleteUsergroup($usergroupID)) {
						
							$this->opm->displayAlert("Usergroup deleted successfully!",'/usergroups/showall/');
							return true;
						
						} else {
						
						
						
						}
					
					
					}
		    	
		    	
		    	}
		    	
		    	$template['content'] = $this->load->view('usergroups/delete',$data,true);
		        
		        $template['page_title'] = "Assign Properties to Usergroups";
		        $template['bigheader'] = "Delete Usergroup";
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
    
    	if (!$usergroupID = $this->input->post('usergroupID'))
				$errors .= "Usergroup must have a value!<br />";

		if ($errors) {
	
			$this->opm->displayError($errors);
			return false;
		
		}	
    	
    	$propertyIDs = $this->input->post('propertyIDs');
 
    	$this->usergroups_model->saveUsergroupProperties($usergroupID, $propertyIDs);
    	
    	$this->opm->displayAlert("Usergroup Properties have been saved!",'/usergroups/assignProperties/'.$usergroupID.'/');
		return true;
    	
    
	}
    
    
    function loadContent($id,$tabName)
    
    {
    
    	if ($tabName == 'basicinfo') {
    		$this->load->model('users_model');
    		$this->load->model('usergroups_model');
    		
    		$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups());
    		//$data['product'] = $this->products_model->fetchProductInfo($id);
    	
    	}
    		
    	$data['user'] = $this->users_model->fetchUserInfo($id);
		echo $this->load->view('users/'.$tabName,$data,true);
		
    
    }
    
    function save($id) {
    	
    	//print_r($_POST);
    	//die();
    
    	$errors = '';
    
    	if (!$usergroup = $this->input->post('usergroup'))
				$errors .= "Usergroup must have a value!<br />";

		if ($errors) {
	
			$this->opm->displayError($errors);
			return false;
		
		}	
    	
    	$perms = $this->input->post('perms');
    	$checked = $this->input->post('chkbox');
    	
    	
    	$this->usergroups_model->saveUsergroupPerms($id, $perms, $checked,$usergroup);
    	
    	$this->opm->displayAlert("Usergroup has been saved!",'/usergroups/edit/'.$id.'/');
		return true;
    	
    	//redirect( , 'location'); 
    
	}
	
	function doAdd() {
    
    	$errors = '';
    
    	if (!$usergroup = $this->input->post('usergroup'))
				$errors .= "Usergroup must have a value!<br />";

		if ($errors) {
	
			$this->opm->displayError($errors);
			return false;
		
		}	
    	
    	$parentusergroupid = $this->input->post('parentusergroupid');
    	
    	$this->usergroups_model->addUsergroup($usergroup, $parentusergroupid);
    	
    	$this->opm->displayAlert("Usergroup has been added!",'/usergroups/showall/');
		return true;
    
	}
    
    
    
}


?>