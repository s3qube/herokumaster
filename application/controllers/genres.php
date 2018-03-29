<?php

class Genres extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('genres_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'administration';
    	
    }
    
    function index() {
    
    	redirect("/genres/showall");
    
    }
    
    function search($searchText = 0, $offset = 0) // index will be the body style list.
	{
	
		if (checkPerms('can_manage_genres',true)) {
					
			//$data['firstLetters'] = $this->genres_model->fetchFirstLetterLinks($firstletter);
		
			$data['totalGenres'] = $this->genres_model->fetchGenres(true,true,$searchText);
			$data['genres'] = $this->genres_model->fetchGenres(false,false,$searchText);
			
			//print_r($data['genres']);
			//die();
			
			$template['nav2'] = $this->load->view('users/nav2',$data,true);
		
			
			$template['page_title'] = "View Genres";
			$template['bigheader'] = "Search Genres";
			$template['searchArea'] = $this->load->view('genres/searchArea',$data,true);
			$template['rightNav'] = $this->load->view('genres/rightNav',$data,true);
			$template['contentNav'] = $this->load->view('genres/searchNav',$data,true);			
			$template['content'] = $this->load->view('genres/search', $data,true);
			
			
	        $arrJS['scripts'] = array('mootools-release-1.11','opm_scripts'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	        
			$this->load->view('global/maintemplate', $template);
			
		}
	
	}
	
	function showall() {
	
		$data['genres'] = $this->genres_model->fetchGenres();

   		$template['page_title'] = "Manage Genres";
   		$template['bigheader'] = "Manage Genres";
    	$template['nav2'] = $this->load->view('users/nav2',$data,true);

    	
    	$template['headInclude'] = '<link rel="stylesheet" type="text/css" href="'.base_url().'resources/mktree.css">';
    	$template['rightNav'] = $this->load->view('genres/rightNav',$data,true);
    	$template['content'] = $this->load->view('genres/showall',$data,true);
    	
    	$arrJS['scripts'] = array('mktree','opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    
    }
    
    function edit($genreid = 'add') {
        
        if ($genreid == 'add') {
        	
        	$data['mode'] = 'add';
        	$template['page_title'] = "Add Genre";
        	$template['bigheader'] = "Add Genre";
       		$template['nav2'] = "Add Genre";
        
        } else {
        
        	$data['mode'] = 'edit';
        	
        	$data['genre'] = $this->genres_model->fetchgenre($genreid);
        	
        	$template['page_title'] = "Edit Genre - " . $data['genre']->genre;
        	$template['bigheader'] = "Edit Genre - " . $data['genre']->genre;
       		$template['nav2'] = "Edit Genre - " . $data['genre']->genre;
        
        }
        
        $data['genres'] = genreArray2Select($this->genres_model->fetchgenresArray(), false);
    	$template['content'] = $this->load->view('genres/edit',$data,true);
        
		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    
   
    
    
    function loadContent($id,$tabName)
    
    {
    
    	if ($tabName == 'basicinfo') {
    		$this->load->model('users_model');
    		$this->load->model('genres_model');
    		
    		$data['genres'] = genreArray2Select($this->genres_model->fetchgenres());
    		//$data['product'] = $this->products_model->fetchProductInfo($id);
    	
    	}
    		
    	$data['user'] = $this->users_model->fetchUserInfo($id);
		echo $this->load->view('users/'.$tabName,$data,true);
		
    
    }
	
	function save() {
    
    	if ($this->input->post('genreid')) { // we are in add mode
    	    	
    		$arrPostData['genreid'] = $this->input->post('genreid');
    	
    	}
    	
    	$errors = '';
    
    	if (!$arrPostData['genre']= $this->input->post('genre'))
				$errors .= "genre must have a value!<br />";

		if ($errors) {
	
			$this->opm->displayError($errors);
			return false;
		
		}	
    	
    	$arrPostData['parentgenreid'] = $this->input->post('parentgenreid');
    	
    	if ($this->genres_model->savegenreInfo($arrPostData)) {
    		
    		if (!isset($arrPostData['genreid'])) {
    		
    			$this->opm->displayAlert("genre has been added!",'/genres/showall/');
				return true;
    		
    		} else {
    		
    			$this->opm->displayAlert("genre has been saved!",'/genres/showall/');
				return true;
    		
    		}
    		
    	
    	}
    	
    	
    
	}
    
    
    
}


?>