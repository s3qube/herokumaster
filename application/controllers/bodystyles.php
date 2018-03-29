<?php

class Bodystyles extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('bodystyles_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'administration';
    
    	global $searchArgList;
    	$searchArgList = array("searchText","firstLetter");
    	
    }

	function index() // index will be the property list.
	{
	
		redirect("/bodystyles/search");	

	}
	
	function search($searchText = 0, $firstletter = null) // index will be the body style list.
	{
	
		if (checkPerms('can_manage_bodystyles',true)) {
					
			$data['firstLetters'] = $this->bodystyles_model->fetchFirstLetterLinks($firstletter);
		
			$data['totalBodystyles'] = $this->bodystyles_model->fetchListBodystyles(true,true,$searchText,0,0,$firstletter);
			$data['bodystyles'] = $this->bodystyles_model->fetchListBodystyles(true,false,$searchText,0,0,$firstletter);
			
			//print_r($data['bodystyles']);
			//die();
			
			$template['nav2'] = $this->load->view('users/nav2',$data,true);
		
			
			$template['page_title'] = "View Body Styles";
			$template['bigheader'] = "Search Body Styles";
			$template['searchArea'] = $this->load->view('bodystyles/searchArea',$data,true);
			$template['rightNav'] = $this->load->view('bodystyles/rightNav',$data,true);
			$template['contentNav'] = $this->load->view('bodystyles/searchNav',$data,true);			
			$template['content'] = $this->load->view('bodystyles/search', $data,true);
			
			
	        $arrJS['scripts'] = array('mootools-release-1.11','opm_scripts'); // 
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
		
		$url = "/bodystyles/search/";
		
		foreach ($segments as $data)
			$url .= $data . "/";
			
		
		redirect($url);
			
	
    
    }
	
	
	
	function view($id) {
    
		$this->load->model('categories_model');
		
		$data = array();
		$data['b'] = $this->bodystyles_model->fetchBodystyle($id);
		
		$data['categories'] = $this->categories_model->fetchCategories();
		
    	$template['content'] = $this->load->view('bodystyles/basicinfo',$data,true);

        $template['nav2'] = "<a href=\"".base_url()."bodystyles/search/\">Body Styles</a>&nbsp;&nbsp;>&nbsp;&nbsp;" . $data['b']->bodystyle;
        $template['page_title'] = "View Body Style - " . $data['b']->bodystyle;
		$template['bigheader'] = $data['b']->bodystyle;

		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','datepicker'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		
		$this->load->view('global/maintemplate', $template);
    }
    
    function add() {
    
    	$this->load->model('categories_model');
    
    	if (checkPerms('can_manage_bodystyles',true)) {
		
			$data = array();
			
	    	$data['categories'] = $this->categories_model->fetchCategories();
	    	$template['content'] = $this->load->view('bodystyles/basicinfo',$data,true);
	    		    	
	        $template['nav2'] = $this->load->view('users/nav2',$data,true);
	        $template['page_title'] = "Add New Body Style";
			$template['bigheader'] = "Add New Body Style";
	
			$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','datepicker'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
			
			$this->load->view('global/maintemplate', $template);
		
		}
  
    }
    
    
    function save() {
	
    	
    	if (checkPerms('can_manage_bodystyles',true)) {
    	
    		$errors = "";
    	
			$postdata['bodystyleid'] = $this->input->post('bodystyleid');
			$postdata['categoryid'] = $this->input->post('categoryid');
			$postdata['code'] = $this->input->post('code');
						

			if (!$postdata['bodystyle'] = $this->input->post('bodystyle'))
				$errors .= "bodystyle Must have a Name!<br />";
			
			
			if ($errors) {
			
				$this->opm->displayError($errors);
				return false;
			
			}
			
			if ($bodystyleid = $this->bodystyles_model->saveBodystyle($postdata)) {
			
				
				$this->opm->displayAlert("Body Style Has Been Saved!","/bodystyles/search/");
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the Body Style!");
				return true;
			}
		
		}
				
	}
	
}
//
?>