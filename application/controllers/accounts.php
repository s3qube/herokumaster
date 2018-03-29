<?php

class Accounts extends CI_Controller {

	function __construct() {
	
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('accounts_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'administration';
    
    	global $searchArgList;
    	$searchArgList = array("showDeactivated");
    	
    }

	function index() {
	
		redirect("/accounts/search");	

	}
	
	function search($showDeactivated = '0', $offset = 0) {
	
		if (checkPerms('can_manage_accounts',true)) {

			global $searchArgList;
			
			$data = array();
	   
			$template['page_title'] = "Manage Accounts";
			$template['bigheader'] = "Manage Accounts";
			$template['nav2'] = $this->load->view('users/nav2',$data,true);
			
			if ($showDeactivated == '0')
				$includeDeactivated = false;
			else
				$includeDeactivated = true;
				
			
			$data['totalAccounts'] = $this->accounts_model->fetchAccounts(true,null,null,null,$includeDeactivated);
			
			foreach ($searchArgList as $k=>$d) 
				$data['args'][$d] = ${$d};
			
			
			$this->load->library('pagination');
			$config['base_url'] = base_url().'/accounts/search/'.$showDeactivated;
			$config['total_rows'] = $data['totalAccounts'];
			$config['per_page'] = '20';
			$config['uri_segment'] = 4;
			
			$this->pagination->initialize($config);
			
			$data['accounts'] = $this->accounts_model->fetchAccounts(false,$offset,$config['per_page'],null,1,"account"); 
			
			$data['prodStart'] = $offset + 1;
			$data['prodEnd'] = $data['prodStart'] + ($data['accounts']->num_rows() - 1);
			
			$template['rightNav'] = $this->load->view('accounts/rightNav',$data,true);
			//$template['searchArea'] = $this->load->view('accounts/searchArea',$data,true);
			$template['contentNav'] = $this->load->view('accounts/searchNav',$data,true);
			$template['content'] = $this->load->view('accounts/search',$data,true);
			
			$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','tipsx3'); // 
			$template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
				
			$this->load->view('global/maintemplate', $template);
			
		}    
    }
	
	function submit() { // construct a search url from a form submission
    
    	global $searchArgList;

		foreach ($searchArgList as $key=>$data) {
			
			if ($this->input->post($data))
				$segments[$data] = $this->input->post($data);
			else
				$segments[$data] = 0;	
			
		}
		
		$url = "/Accounts/search/";
		
		foreach ($segments as $data)
			$url .= $data . "/";
			
		
		redirect($url);
			
	
    
    }
    
    function toggleActivation($accountid) {
    
    	
    	if (checkPerms('can_manage_accounts',true)) {
    	
    		
    		// get existing account status
    		
    		$a = $this->accounts_model->fetchAccount($accountid);
    		
    		if ($a->isactive) { // account is active, deactivate.
    		
    			if ($this->accounts_model->changeAccountStatus($accountid,0)) {
    			
    				$this->opm->displayAlert("Account Has Been Deactivated!","/accounts/search/");
    				return true;
    			
    			} else {
    			
    				$this->opm->displayError("Account status could not be changed!");
    				return false;
    			
    			}
    					
    		
    		} else { // account is disabled, active.
    		
    			if ($this->accounts_model->changeAccountStatus($accountid,1)) {
    			
    				$this->opm->displayAlert("Account Has Been Activated!","/accounts/search/");
    				return true;
    			
    			} else {
    			
    				$this->opm->displayError("Account status could not be changed!");
    				return false;
    			
    			}
    		
    		}
    		
    	
    	}
    
    }
    
	
	
	function view($id,$tabname = 'basicinfo') {
    
		
		$data = array();
		$data['a'] = $this->accounts_model->fetchAccount($id);
		
    	$template['content'] = $this->load->view('accounts/basicinfo',$data,true);

        $template['nav2'] = "<a href=\"".base_url()."accounts/search/\">Accounts</a>&nbsp;&nbsp;>&nbsp;&nbsp;" . $data['p']->account;
        $template['page_title'] = "View account - " . $data['p']->account;
		$template['bigheader'] = $data['p']->account;

		$arrJS['scripts'] = array('opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		$this->load->view('global/maintemplate', $template);
   
    }
    
    function add() {
    
    	if (checkPerms('can_manage_accounts',true)) {
		
			$data = array();
			
	    	$template['content'] = $this->load->view('accounts/basicinfo',$data,true);
	    	
	        $template['nav2'] = $this->load->view('users/nav2',$data,true);
	        $template['page_title'] = "Add New Account";
			$template['bigheader'] = "Add New Account";
	
			$arrJS['scripts'] = array('opm_scripts'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
			
			$this->load->view('global/maintemplate', $template);
		
		}
  
    }
    
    
    function save() {
	
    	
    	if (checkPerms('can_manage_accounts',true)) {
    	
    		$errors = "";
    	
			$postdata['accountid'] = $this->input->post('accountid');
						
			if (!$postdata['account'] = $this->input->post('account'))
				$errors .= "Account Must have a Name!<br />";
			
			
			if ($errors) {
			
				$this->opm->displayError($errors);
				return false;
			
			}
			
			if ($accountid = $this->accounts_model->saveAccountInfo($postdata)) {
			
				
				$this->opm->displayAlert("Account Has Been Saved!","/accounts/search/");
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the Account!");
				return true;
			}
		
		}
				
	}
	
}
//
?>