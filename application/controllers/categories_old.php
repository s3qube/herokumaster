<?php

class Categories extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('categories_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'administration';
    
    	global $searchArgList;
    	$searchArgList = array("searchText","firstLetter");
    	
    }

	function index() // index will be the property list.
	{
	
		redirect("/categories/search");	

	}
	
	function search($searchText = 0, $firstletter = null) // index will be the property list.
	{
	
		if (checkPerms('can_manage_categories',true)) {
		
			$searchText = urldecode($searchText);
		
			$data['firstLetters'] = $this->categories_model->fetchFirstLetterLinks($firstletter);
		
			$data['totalCategories'] = $this->categories_model->fetchListCategories(true,true,true,$searchText,0,0,$firstletter);
			$data['categories'] = $this->categories_model->fetchListCategories(true,true,false,$searchText,0,0,$firstletter);
			$template['nav2'] = $this->load->view('users/nav2',$data,true);
		
			
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
	
	
	
	function view($id) {
    
		
		$data = array();
		$data['p'] = $this->categories_model->fetchCategoryInfo($id);
		
    	$template['content'] = $this->load->view('categories/basicinfo',$data,true);

        $template['nav2'] = "<a href=\"".base_url()."categories/search/\">Categories</a>&nbsp;&nbsp;>&nbsp;&nbsp;" . $data['p']->category;
        $template['page_title'] = "View Category - " . $data['p']->category;
		$template['bigheader'] = $data['p']->category;

		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','datepicker'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		
		$this->load->view('global/maintemplate', $template);
    }
    
    function add() {
    
    	if (checkPerms('can_manage_categories',true)) {
		
			$data = array();
			
	    	$template['content'] = $this->load->view('categories/basicinfo',$data,true);
	    	
	        $template['nav2'] = $this->load->view('users/nav2',$data,true);
	        $template['page_title'] = "Add New Category";
			$template['bigheader'] = "Add New Category";
	
			$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','datepicker'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
			
			$this->load->view('global/maintemplate', $template);
		
		}
  
    }
    
    
    function save() {
	
    	
    	if (checkPerms('can_manage_categories',true)) {
    	
    		$errors = "";
    	
			$postdata['categoryid'] = $this->input->post('categoryid');
						

			if (!$postdata['category'] = $this->input->post('category'))
				$errors .= "Category Must have a Name!<br />";
			
			
			if ($errors) {
			
				$this->opm->displayError($errors);
				return false;
			
			}
			
			if ($categoryid = $this->categories_model->saveCategoryInfo($postdata)) {
			
				
				$this->opm->displayAlert("Category Has Been Saved!","/categories/view/" . $categoryid);
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the Category!");
				return true;
			}
		
		}
				
	}
	
}
//
?>