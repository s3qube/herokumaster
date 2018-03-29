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
    	
    }
    
    function index() {
    
    	redirect("/categories/showall");
    
    }
	
	function showall() {
	
		$data['categories'] = categoryArray2ul($this->categories_model->fetchCategoriesArray(),false,true);

   		$template['page_title'] = "Manage Categories";
   		$template['bigheader'] = "Manage Categories";
    	$template['nav2'] = $this->load->view('users/nav2',$data,true);

    	
    	$template['headInclude'] = '<link rel="stylesheet" type="text/css" href="'.base_url().'resources/mktree.css">';
    	$template['rightNav'] = $this->load->view('categories/rightNav',$data,true);
    	$template['content'] = $this->load->view('categories/showall',$data,true);
    	
    	$arrJS['scripts'] = array('mktree','opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    
    }
    
    function edit($categoryid = 'add') {
        
        if ($categoryid == 'add') {
        	
        	$data['mode'] = 'add';
        	$template['page_title'] = "Add Category";
        	$template['bigheader'] = "Add Category";
       		$template['nav2'] = "Add Category";
        
        } else {
        
        	$data['mode'] = 'edit';
        	
        	$data['category'] = $this->categories_model->fetchCategory($categoryid);
        	
        	$template['page_title'] = "Edit Category - " . $data['category']->category;
        	$template['bigheader'] = "Edit Category - " . $data['category']->category;
       		$template['nav2'] = "Edit Category - " . $data['category']->category;
        
        }
        
        $data['categories'] = categoryArray2Select($this->categories_model->fetchCategoriesArray(), false);
    	$template['content'] = $this->load->view('categories/edit',$data,true);
        
		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    
   
    
    
    function loadContent($id,$tabName)
    
    {
    
    	if ($tabName == 'basicinfo') {
    		$this->load->model('users_model');
    		$this->load->model('categories_model');
    		
    		$data['categories'] = categoryArray2Select($this->categories_model->fetchcategories());
    		//$data['product'] = $this->products_model->fetchProductInfo($id);
    	
    	}
    		
    	$data['user'] = $this->users_model->fetchUserInfo($id);
		echo $this->load->view('users/'.$tabName,$data,true);
		
    
    }
	
	function save() {
    
    	if ($this->input->post('categoryid')) { // we are in add mode
    	    	
    		$arrPostData['categoryid'] = $this->input->post('categoryid');
    	
    	}
    	
    	$errors = '';
    
    	if (!$arrPostData['category']= $this->input->post('category'))
				$errors .= "Category must have a value!<br />";

		if ($errors) {
	
			$this->opm->displayError($errors);
			return false;
		
		}	
    	
    	$arrPostData['parentcategoryid'] = $this->input->post('parentcategoryid');
    	
    	if ($this->categories_model->saveCategoryInfo($arrPostData)) {
    		
    		if (!isset($arrPostData['categoryid'])) {
    		
    			$this->opm->displayAlert("Category has been added!",'/categories/showall/');
				return true;
    		
    		} else {
    		
    			$this->opm->displayAlert("Category has been saved!",'/categories/showall/');
				return true;
    		
    		}
    		
    	
    	}
    	
    	
    
	}
    
    
    
}


?>